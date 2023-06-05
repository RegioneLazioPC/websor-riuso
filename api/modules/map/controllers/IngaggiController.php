<?php

namespace api\modules\map\controllers;

use Exception;
use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use common\models\VolOrganizzazione;

use common\models\ViewCartografiaAutomezzo;
use common\models\ViewCartografiaAttrezzatura;
use api\utils\ResponseError;

use common\models\UtlIngaggioSearchForm;
use common\models\UtlAggregatoreTipologie;
use common\models\UtlAutomezzo;
use common\models\UtlAttrezzatura;
use common\models\VolSede;
use common\models\UtlIngaggio;
use common\models\ConOperatoreTask;

/**
 * Ingaggi Controller
 *
 */
class IngaggiController extends ActiveController
{
    public $modelClass = 'common\models\UtlEvento';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] =[
                'class' => \api\utils\Authenticator::class,
                'except' => ['options','search']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options','search'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['engage','search' ],
                    'roles' => ['@']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Di default per il metodo options torniamo ok in modo da non avere errori not found dalle chiamate automatiche del browser
     * @return [type] [description]
     */
    public function actionOptions() {
        return ['message'=>'ok'];
    }

    /**
     * Cerca risorse per attivazione
     * @return [type] [description]
     */
    public function actionSearch() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $connection = Yii::$app->getDb();



        $params = Yii::$app->request->get();

        $key = $params['lat']."_".$params['lon'];
        
            $command = $connection->createCommand("
                SELECT * FROM (WITH point_selected AS (SELECT 
                        (st_setsrid
                            (st_makepoint
                                (:lon::DOUBLE precision, :lat::DOUBLE precision), 4326
                            )
                        ) AS geom
                    ) 
                    SELECT ROW_NUMBER() 
                        OVER(ORDER BY dc.agg_cost ASC) n_ord, dc.start_vid, dc.end_vid, 
                    round( CAST(float8 (dc.agg_cost / 1000)::text AS numeric), 2) AS dist_km, 
                    f.* 
                    FROM 
                    pgr_dijkstracost( 'SELECT gid as id, source, target, length_m as cost FROM routing.osm_ways'::text, 
                        ( SELECT vert.id FROM routing.osm_ways_vertices_pgr vert
                            WHERE vert.main_network = true 
                            ORDER BY (vert.the_geom::geography <-> (SELECT geom FROM point_selected)::geography) LIMIT 1 
                        ), 
                        ARRAY( SELECT nvf.id_vert FROM view_routing_organizzazioni nvf ), 
                        false)
                     dc(start_vid, end_vid, agg_cost) 
                INNER JOIN view_routing_organizzazioni near ON near.id_vert = dc.end_vid 
                INNER JOIN view_organizzazioni f ON f.id_sede = near.id_sede WHERE 
                tipologia_risorsa='sede'
                ORDER BY n_ord ASC LIMIT 10000 ) AS \"vtable\"" )
            ->bindValue ( ':lat', $params['lat'] )
            ->bindValue ( ':lon', $params['lon'] );
            //return $command->getRawSql();
            $result = $command->queryAll();
            $sedi = [];
            foreach ($result as $r) {
                $sedi[$r['id_sede']] = $r;
            }
            

        
        /**
         * Da qui la query vera e propria in cui poi sostituisco la distanza inserendo quella stradale
         */
        if(!Yii::$app->request->get('distance')) $params['distance'] = 200;
                
                

        $fields = [
            'DISTINCT(CONCAT( ref_id, \'_\', tipologia_risorsa ) ) riferimento',
            'codice_associazione',
            'ref_id',
            'ref_identifier',
            'denominazione_organizzazione',
            'tipologia_risorsa',
            'ref_engaged',
            'ref_tipo_id',
            'ref_tipo_descrizione',
            'id_sede',
            'lat',
            'lon',
            'indirizzo_sede',
            'view_organizzazioni.id_organizzazione'
        ];
        $group = [
            'codice_associazione',
            'ref_id',
            'ref_identifier',
            'denominazione_organizzazione',
            'tipologia_risorsa',
            'ref_engaged',
            'ref_tipo_id',
            'ref_tipo_descrizione',
            'id_sede',
            'lat',
            'lon',
            'indirizzo_sede',
            'view_organizzazioni.id_organizzazione',
            'geom_sede'
        ];
        $query = UtlIngaggioSearchForm::find()
        ->select( $fields  )
        ->with(['sezioneSpecialistica', 'contattiAttivazioni', 'contattiAttivazioni.contatto'])
        ->groupBy($group)
        ;

        
        if(Yii::$app->request->get('num_comunale')) $query->andWhere(['num_comunale'=>$params['num_comunale']]);
        if(Yii::$app->request->get('id_comune')) $query->andWhere(['id_comune'=>$params['id_comune']]);
        if(Yii::$app->request->get('id_provincia')) $query->andWhere(['id_provincia'=>$params['id_provincia']]);
        
        
        if(Yii::$app->request->get('id_organizzazione')) $query->andWhere(['id_organizzazione'=>$params['id_organizzazione']]);


        /**
         * è selezionata una categoria e non tipologia
         */
        if(Yii::$app->request->get('id_categoria') && 
            (!Yii::$app->request->get('id_tipologia') || empty(Yii::$app->request->get('id_tipologia')))
        ) :

            $cats = $params['id_categoria'];
            if(!in_array("0", $cats)) :

                $aggregatori = UtlAggregatoreTipologie::find()
                                ->where(['id_categoria'=>$cats])
                                ->all();
                
                $tipi_automezzo_id = [];
                $tipi_attrezzature_id = [];

                foreach ($aggregatori as $aggregatore) {
                    $tipi_automezzo = $aggregatore->getTipiAutomezzo()->all();
                    $tipi_attrezzature = $aggregatore->getTipiAttrezzatura()->all();
                    
                    foreach ($tipi_automezzo as $automezzo) {
                        $tipi_automezzo_id[] = $automezzo->id;
                    }

                    foreach ($tipi_attrezzature as $attrezzatura) {
                        $tipi_attrezzature_id[] = $attrezzatura->id;
                    }

                }


                $query->andWhere(['or',
                    ['tipo_attrezzatura_id'=>$tipi_attrezzature_id],
                    ['tipo_automezzo_id'=>$tipi_automezzo_id]
                ]);

            endif;

        endif;


        if(Yii::$app->request->get('specializzazioni')) {
            
            $query
            ->joinWith('sezioneSpecialistica')
            ->andWhere(
                ['tbl_sezione_specialistica.id' => Yii::$app->request->get('specializzazioni')]
            );
         
        }

        
        if(Yii::$app->request->get('id_tipologia')) :
            $aggregatori = UtlAggregatoreTipologie::find()
                            ->where(['id'=>$params['id_tipologia']])
                            ->all();

            $tipi_automezzo_id = [];
            $tipi_attrezzature_id = [];

            foreach ($aggregatori as $aggregatore) {
                $tipi_automezzo = $aggregatore->getTipiAutomezzo()->all();
                $tipi_attrezzature = $aggregatore->getTipiAttrezzatura()->all();
                
                foreach ($tipi_automezzo as $automezzo) {
                    $tipi_automezzo_id[] = $automezzo->id;
                }

                foreach ($tipi_attrezzature as $attrezzatura) {
                    $tipi_attrezzature_id[] = $attrezzatura->id;
                }
            }

            $query->andWhere(['or',
                ['tipo_attrezzatura_id'=>$tipi_attrezzature_id],
                ['tipo_automezzo_id'=>$tipi_automezzo_id]
            ]);
        endif;

        if(Yii::$app->request->get('id_utl_automezzo_tipo') && Yii::$app->request->get('id_utl_attrezzatura_tipo')) :
            // sono presenti entrambi, devo cercare automezzi con attrezzatura
            $query->andWhere(['or',
                [
                    'tipologia_risorsa'=>'automezzo', 
                    'ref_tipo_id'=>$params['id_utl_automezzo_tipo']
                ],
                [
                    'tipologia_risorsa'=>'attrezzatura', 
                    'ref_tipo_id'=>$params['id_utl_attrezzatura_tipo']
                ]
            ]);
        elseif (Yii::$app->request->get('id_utl_automezzo_tipo') && !Yii::$app->request->get('id_utl_attrezzatura_tipo')) :
            
            // cerca un automezzo
            $query->andWhere([
                'tipologia_risorsa'=>'automezzo', 
                'ref_tipo_id'=>$params['id_utl_automezzo_tipo'], 
            ]);
        elseif (!Yii::$app->request->get('id_utl_automezzo_tipo') && Yii::$app->request->get('id_utl_attrezzatura_tipo')) :
            
            // cerca una attrezzatura
            $query->andWhere([
                'tipologia_risorsa'=>'attrezzatura', 
                'ref_tipo_id'=>$params['id_utl_attrezzatura_tipo'], 
            ]);
        endif;
        
        /**
         * Ordinamento in base a mappatura definita nel model
         */
        $sort_order = Yii::$app->request->get('type_ord') && Yii::$app->request->get('type_ord') == 'asc' ? SORT_ASC : SORT_DESC;
        
        $orders = [];
        if(Yii::$app->request->get('sort')) :
            $orders[Yii::$app->request->get('sort')] = $sort_order;
        endif;

        
        if(!empty($orders)) $query->orderBy($orders);
        
        $per_page = 30;
        
        $orgs = $query->asArray()->all();

                
        $res = [];
        foreach ($orgs as $org) {            
            $res[] = array_merge( $org, 
                ['dist_km'=>(isset($sedi["".$org['id_sede']])) ? $sedi["".$org['id_sede']]['dist_km'] : 999999 ] );
        }

        
        ArrayHelper::multisort($res, ['dist_km'], SORT_ASC);
        
        
        $return = [
            "type"=>"FeatureCollection",
            "features"=>[]
        ];
        $n=0;
        // solo test bottleneck
        $empty = [];
        foreach ($res as $el) {            
            
            $return["features"][] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates"=> [$el['lon'], $el['lat']],
                ],
                "properties" => $el
            ];  
            $n++;  
        }
        $return['count'] = count($return['features']);
        
        return $return;
    }

    /**
     * Attiva risorsa
     * @return [type] [description]
     */
    public function actionEngage() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        switch(Yii::$app->request->get('ref_type')){
            case 'attrezzatura':
                $obj = UtlAttrezzatura::find()->where(['id'=>Yii::$app->request->get('ref_id')])->with(['sede', 'sede.locComune', 'sede.organizzazione', 'sede.locComune.provincia'])->one();
                if(!$obj || $obj->engaged) ResponseError::returnSingleError(422, "Non ingaggiabile ".$obj->engaged);
            break;
            case 'automezzo':
                $obj = UtlAutomezzo::find()->where(['id'=>Yii::$app->request->get('ref_id')])->with(['sede', 'sede.locComune', 'sede.organizzazione', 'sede.locComune.provincia'])->one();
                if(!$obj || $obj->engaged) ResponseError::returnSingleError(422, "Non ingaggiabile ".$obj->engaged);
            break;
            default:
                $obj = VolSede::find()->where(['id'=>Yii::$app->request->get('ref_id')])->with(['locComune', 'organizzazione', 'locComune.provincia'])->one();
                if(!$obj) ResponseError::returnSingleError(422, "Non ingaggiabile");
            break;
        }

        $ingaggio = new UtlIngaggio();
        $ingaggio->idevento = Yii::$app->request->get('event_id');
        
        if(Yii::$app->request->get('ref_type') == 'automezzo' || Yii::$app->request->get('ref_type') == 'attrezzatura') :
            $ingaggio->idorganizzazione = $obj->sede->organizzazione->id;
            $ingaggio->idsede = $obj->sede->id;
        else:
            $ingaggio->idorganizzazione = $obj->organizzazione->id;
            $ingaggio->idsede = $obj->id;
        endif;
        if(Yii::$app->request->get('ref_type') == 'automezzo') $ingaggio->idautomezzo = Yii::$app->request->get('ref_id');
        if(Yii::$app->request->get('ref_type') == 'attrezzatura') $ingaggio->idattrezzatura = Yii::$app->request->get('ref_id');

        if(!$ingaggio->save()) ResponseError::returnSingleError(422, $ingaggio->getErrors());

        $task_name = 'Richiesta nuova attivazione';
        if(Yii::$app->request->get('ref_type') == 'automezzo' && $obj && $obj->targa) :
            $task_name .= " - " . $obj->targa . " -";
        endif;
        $diarioEvento = new ConOperatoreTask();
        $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
        $diarioEvento->idtask = 4; //DATI CABLATI NEL DB
        $diarioEvento->idevento = $ingaggio->idevento;
        $diarioEvento->note = $task_name.' '. $ingaggio->organizzazione->denominazione ;
        $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

        if(!($diarioEvento->save())){
            ResponseError::returnSingleError(422, $diarioEvento->getErrors());
        }

        return ['message'=>'ok'];
    }

}
