<?php

namespace api\modules\v1\controllers;

use common\models\ConOperatoreTask;
use common\models\ConEventoExtra;
use common\models\ConEventoSegnalazione;
use common\models\ConSegnalazioneExtra;
use common\models\ConUtenteExtra;
use common\models\MyHelper;
use common\models\UtlEvento;
use common\models\UtlExtraSegnalazione;
use common\models\UtlExtraUtente;
use common\models\UtlOperatorePc;
use common\models\UtlSegnalazioneAttachments;
use common\models\UtlUtente;
use common\models\UtlAnagrafica;
use common\models\ViewEventi;
use common\models\ViewSegnalazioni;
use common\models\UtlRuoloSegnalatore;

use Exception;
use Yii;
use common\models\UtlSegnalazione;
use common\models\UtlSegnalazioneSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use common\models\UtlIngaggio;
use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzo;
use common\models\VolSede;
use common\models\UtlIngaggioSearchForm;
use common\models\UtlAggregatoreTipologie;


use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\LocProvincia;
use common\models\LocComune;
use common\models\UtlSpecializzazione;

use common\models\UtlTipologia;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use GuzzleHttp\Client;

/**
 * Map Controller API
 *
 * Controller per la gestione dei servizi della cartografia
 * @deprecated utilizzare il modulo apposito per evitare l'esposizione all'esterno del controller
 */
class MapController extends ActiveController
{
    public $modelClass = 'common\models\UtlEvento';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),   
           
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },

            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create-event', 'eventi-set-lat-lon'],
                    'permissions' => ['createEvento']
                ],
                [
                    'allow' => true,
                    'actions' => ['create-segnalazione'],
                    'permissions' => ['createSegnalazione']
                ],
                [
                    'allow' => true,
                    'actions' => [
                        'get-associazioni', 'get-all', 'get-sede',
                        'radio','elicotteri','address'
                    ],
                    'permissions' => ['@']
                ],
                [
                    'allow' => true,
                    'actions' => ['change-to-event', 'attach-evento'],
                    'permissions' => ['transformSegnalazioneToEvento']
                ],
                [
                    'allow' => true,
                    'actions' => ['event-near', 'eventi'],
                    'permissions' => ['listEventi']
                ],
                [
                    'allow' => true,
                    'actions' => ['segnalazioni'],
                    'permissions' => ['listSegnalazioni']
                ],
                [
                    'allow' => true,
                    'actions' => ['get-ingaggi', 'get-fronti', 'get-segnalazioni'],
                    'permissions' => ['viewEvento']
                ],
                [
                    'allow' => true,
                    'actions' => ['engage','query'],
                    'permissions' => ['createIngaggio']
                ],
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
     * Create event
     * @return [type] [description]
     */
    public function actionCreateEvent() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = MyHelper::getCoordinateLocalita(Yii::$app->request->post());
        $model = new UtlEvento();
        $model->scenario = UtlEvento::SCENARIO_CREATE;
        
        $model->lat = Yii::$app->request->post('lat');
        $model->lon = Yii::$app->request->post('lon');
        $model->tipologia_evento = Yii::$app->request->post('tipologia_evento');
        $model->sottotipologia_evento = Yii::$app->request->post('sottotipologia_evento');
        if(Yii::$app->request->post('idparent')) $model->idparent = Yii::$app->request->post('idparent');
        $model->luogo = (Yii::$app->request->post('luogo')) ? Yii::$app->request->post('luogo') : '';
        $model->indirizzo = (Yii::$app->request->post('indirizzo')) ? Yii::$app->request->post('indirizzo') : '';
        $model->idcomune = (isset($data['idcomune']) && $data['idcomune'] != '') ? $data['idcomune'] : (Yii::$app->request->post('comune') ? Yii::$app->request->post('comune') : null);
        $model->stato = Yii::$app->request->post('stato');

        if(!$model->save()) return ['message'=>'error', 'error'=>json_encode($model->getErrors())];

        return ['message'=>'ok', 'event_id'=>$model->id];

    }


    /**
     * Create segnalazione
     * @return [type] [description]
     */
    public function actionCreateSegnalazione() {
        Yii::$app->response->format = Response::FORMAT_JSON;        

        
        $data = MyHelper::getCoordinateLocalita(Yii::$app->request->post());
        
        $model = new UtlSegnalazione();
        $model->load(['UtlSegnalazione'=>Yii::$app->request->post()]);

        $utente = new UtlUtente();
        $utente->scenario = 'createSegnalatore';
        
        $model->idcomune = (isset($data['idcomune']) && $data['idcomune'] != '') ? 
        $data['idcomune'] : 
        ((Yii::$app->request->post('idcomune')) ? Yii::$app->request->post('idcomune') : null);

        $model->stato = 'Nuova in lavorazione';

        $operatore = UtlOperatorePc::find()->where(['iduser' => Yii::$app->user->id])->one();
        if(isset($operatore)) $model->idsalaoperativa = $operatore->idsalaoperativa;

        if(!$model->save()) return ['message'=>'error', 'error'=>json_encode($model->getErrors())];

        //return Yii::$app->request->post();

        $anagrafica = new UtlAnagrafica();
        $anagrafica->load(['UtlAnagrafica'=>Yii::$app->request->post()]);
        $anagrafica = $anagrafica->createOrUpdate();

        if($anagrafica->getErrors()) :
            $model->delete();
            return ['message'=>'error', 'error'=>json_encode($anagrafica->getErrors())];
        endif;

        $utente->load(['UtlUtente'=>Yii::$app->request->post()]);
        $utente->id_anagrafica = $anagrafica->id;
        $utente->tipo = Yii::$app->request->post('tipo');
        // Salvataggio utente
        if( !$utente->save(false) ) :
            $model->delete();
            return ['message'=>'error', 'error'=>json_encode($utente->getErrors())];
        endif;

        $model->idutente = $utente->getPrimaryKey();
        $model->save();

        return ['message'=>'ok', 'segnalazione_id'=>$model->id];

    }

    public function actionGetAssociazioni() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $els = UtlIngaggioSearchForm::find()->where(['tipologia_risorsa'=>'sede'])->orderBy(['denominazione_organizzazione'=>SORT_ASC])->all();
        // trasformo in geojson        
        $geo = [
            "type"=>"FeatureCollection",
            "features"=>[]
        ];

        foreach ($els as $el) {
            $geo["features"][] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates"=> [$el->lon, $el->lat],
                ],
                "properties" => $el
            ];
        }

        return $geo;
    }

    public function actionGetAll() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $mappatura_categorie_aggregatori = UtlAggregatoreTipologie::find()->with(['categoria'])->all();
        $temp_agg = [];
        foreach ($mappatura_categorie_aggregatori as $aggregatore) {
            $temp_agg[$aggregatore->id] = $aggregatore->categoria->id;
        }

        $mappatura_categorie_aggregatori = $temp_agg;

        return [
            'tipo_automezzi' => UtlAutomezzoTipo::find()->with(['aggregatori','aggregatori.categoria'])->asArray()->all(),
            'tipo_attrezzature' => UtlAttrezzaturaTipo::find()->with(['aggregatori','aggregatori.categoria'])->asArray()->all(),
            'aggregatori' => UtlAggregatoreTipologie::find()->all(),
            'categorie' => UtlCategoriaAutomezzoAttrezzatura::find()->all(),
            'province' => LocProvincia::find()->where(['id_regione'=>Yii::$app->params['region_filter_id']])->all(),
            'comuni' => LocComune::find()->joinWith(['provincia'])->asArray()->where(['loc_provincia.id_regione'=>Yii::$app->params['region_filter_id']])->orderBy('comune')->all(),
            'specializzazioni'=>UtlSpecializzazione::find()->all(),
            'tipi_evento' => UtlTipologia::find()->all(),
            'ruoli_segnalatore' => UtlRuoloSegnalatore::find()->all(),
            'fonti_segnalazione' => UtlSegnalazione::getFonteArray(),
            'mappatura_categorie_aggregatori' => $mappatura_categorie_aggregatori
        ];
    }

    /**
     * Trasforma segnalazione in evento
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionChangeToEvent($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = UtlSegnalazione::find()->where(['id'=>$id])->one();
        $data = $model->attributes;
        unset($data['stato']);

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {

            // Creo evento
            $eventoModel = new UtlEvento();
            $eventoModel->setAttributes($data);
            $eventoModel->stato = 'Non gestito';
            if(!$eventoModel->save(false)){
                throw new Exception('Errore salvataggio Evento', 500);
            }

            // Salvo gli extra segnalazione/evento
            $extras = $model->extras;
            if(!empty($extras)){

                foreach ($extras as $extra){
                    $eventoModel->link('extras', $extra);

                    $conSegnalazioneExtra = ConSegnalazioneExtra::find()->where(['idsegnalazione' => $model->id, 'idextra' => $extra->id])->one();
                    $conEventoExtra = ConEventoExtra::find()->where(['idevento' => $eventoModel->id, 'idextra' => $extra->id])->one();
                    
                    if(isset($extra)){
                        $dataEvento = $conSegnalazioneExtra->attributes;
                        $conEventoExtra->setAttributes($dataEvento);
                        $conEventoExtra->save();
                    }
                }
            }

            // Creo connessione con Segnalazione
            $conEventoSegnalazione = new ConEventoSegnalazione();
            $conEventoSegnalazione->idsegnalazione = $id;
            $conEventoSegnalazione->idevento = $eventoModel->id;
            $conEventoSegnalazione->save();
            if(!$conEventoSegnalazione->save(false)){
                throw new Exception('Errore salvataggio Connessione Segnalazione', 500);
            }

            // Cambio stato alla segnalazione
            $model->stato = 'Verificata e trasformata in evento';
            if(!$model->save(false)){
                throw new Exception('Errore salvataggio stato Segnalazione', 500);
            }

            $dbTrans->commit();

        }catch (Exception $e) {
            $dbTrans->rollBack();

            return ['message'=>'error'];
        }

        return ['message'=>'ok'];
    }

    /**
     * Associa evento a segnalazione
     * @param  [type] $id       [description]
     * @param  [type] $idEvento [description]
     * @return [type]           [description]
     */
    public function actionAttachEvento($id, $idEvento)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = UtlSegnalazione::findOne($id);

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {

            // Creo connessione con Segnalazione
            $conEventoSegnalazione = new ConEventoSegnalazione();
            $conEventoSegnalazione->idsegnalazione = $id;
            $conEventoSegnalazione->idevento = $idEvento;
            $conEventoSegnalazione->save();
            if(!$conEventoSegnalazione->save(false)){
                throw new Exception('Errore salvataggio Connessione Segnalazione', 500);
            }

            // Cambio stato alla segnalazione
            $model->stato = 'Verificata e trasformata in evento';
            if(!$model->save(false)){
                throw new Exception('Errore salvataggio stato Segnalazione', 500);
            }

            $dbTrans->commit();

        }catch (Exception $e) {
            $dbTrans->rollBack();

            return ['message'=>'error'];
        }

        return ['message'=>'ok'];
    }

    /**
     * Prendi eventi vicini a delle coordinate
     * @param  [type] $lat [description]
     * @param  [type] $lon [description]
     * @return [type]      [description]
     */
    public function actionEventNear($lat, $lon) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $events = UtlEvento::find()
        ->select( ['*', 'ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) as distance'] )
        ->with(['tipologia','sottotipologia','comune','comune.provincia'])
        ->where('ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) <= :distance')
        ->addParams([
            ':lat' => floatval($lat), 
            ':lon' => floatval($lon), 
            ':distance' => intval(150000)
        ])
        ->orderBy(['distance'=>SORT_ASC])
        ->asArray()
        ->all();

        return $events;
    }

    /**
     * Restituisci dati della sede da mostrare
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionGetSede($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sede = VolSede::find()->where(['id'=>$id])
        ->with(['organizzazione','locComune','locComune.provincia',
            'automezzi','automezzi.tipo',
            'attrezzature','attrezzature.tipo'])
        ->asArray()
        ->one();

        return $sede;
    }

    /**
     * Prendi ingaggi di un evento
     * @param  [type] $idevento [description]
     * @return [type]           [description]
     */
    public function actionGetIngaggi($idevento) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ingaggi = UtlIngaggio::find()->where(['idevento'=>$idevento])
        ->with(['attrezzatura','attrezzatura.tipo',
                'automezzo','automezzo.tipo',
                'organizzazione',
                'sede', 'sede.locComune', 'sede.locComune.provincia'])
        ->asArray()
        ->all();

        return $ingaggi;
    }

    /**
     * Prendi fronti evento
     * @param  [type] $idevento [description]
     * @return [type]           [description]
     */
    public function actionGetFronti($idevento) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $fronti = UtlEvento::find()->where(['idparent'=>$idevento])
        ->with(['comune', 'comune.provincia','tipologia','sottotipologia'])
        ->asArray()
        ->all();

        return $fronti;
    }

    /**
     * Prendi segnalazioni di un evento
     * @param  [type] $idevento [description]
     * @return [type]           [description]
     */
    public function actionGetSegnalazioni($idevento) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $e = UtlEvento::find()->where(['id'=>$idevento])->one();
        if(!$e) return [];



        return $e->getSegnalazioniAll()->with(['tipologia','comune','comune.provincia'])->asArray()->all();
    }


    /**
     * Ingaggia associazione
     * // params: ref_id, ref_type (automezzo, attrezzatura), event_id
     * @return [type] [description]
     */
    public function actionEngage() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        switch(Yii::$app->request->get('ref_type')){
            case 'attrezzatura':
                $obj = UtlAttrezzatura::find()->where(['id'=>Yii::$app->request->get('ref_id')])->with(['sede', 'sede.locComune', 'sede.organizzazione', 'sede.locComune.provincia'])->one();
                if(!$obj || $obj->engaged) return ['error'=>'Non ingaggiabile'];
            break;
            case 'automezzo':
                $obj = UtlAutomezzo::find()->where(['id'=>Yii::$app->request->get('ref_id')])->with(['sede', 'sede.locComune', 'sede.organizzazione', 'sede.locComune.provincia'])->one();
                if(!$obj || $obj->engaged) return ['error'=>'Non ingaggiabile'];
            break;
            default:
                $obj = VolSede::find()->where(['id'=>Yii::$app->request->get('ref_id')])->with(['locComune', 'organizzazione', 'locComune.provincia'])->one();
                if(!$obj) return ['error'=>'Non ingaggiabile'];
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

        if(!$ingaggio->save()) return ['error'=>$ingaggio->getErrors()];

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
            return ['error'=>$diarioEvento->getErrors()];
        }

        return ['message'=>'ok'];
    }
    

    public function actionQuery() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $connection = Yii::$app->getDb();


        $q_string = "";
        
        $q_parts = [];

        $q_string .= " ref_tipo_id IN (:tipi)";

        $params = Yii::$app->request->get();
        $key = $params['lat']."_".$params['lon'];
        $sedi = Yii::$app->cache->get($key);
        
        if(!$sedi) :
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
                            ORDER BY (vert.the_geom::geography <-> (SELECT geom FROM point_selected)::geography) LIMIT 1 
                        ), 
                        ARRAY( SELECT nvf.id_vert FROM routing.m_view_near_vert nvf WHERE geom_vert::geography <-> (SELECT geom FROM point_selected)::geography < (1000 * 1000) ), 
                        false)
                     dc(start_vid, end_vid, agg_cost) 
                INNER JOIN routing.m_view_near_vert near ON near.id_vert = dc.end_vid 
                INNER JOIN view_organizzazioni f ON f.id_sede = near.id_sede WHERE 
                tipologia_risorsa='sede'
                ORDER BY n_ord ASC LIMIT 10000 ) AS \"vtable\" " )
            ->bindValue ( ':lat', $params['lat'] )
            ->bindValue ( ':lon', $params['lon'] );
            //return $command->getRawSql();
            $result = $command->queryAll();
            $sedi = [];
            foreach ($result as $r) {
                $sedi[$r['id_sede']] = $r;
            }
            Yii::$app->cache->set($key, $sedi, 60*60);
            
        endif;


        
        /**
         * Da qui la query vera e propria in cui poi sostituisco la distanza inserendo quella stradale
         */
        if(!Yii::$app->request->get('distance')) $params['distance'] = 200;
                
                

        $fields = [
            '*'
        ];
        $query = UtlIngaggioSearchForm::find()
        ->select( array_merge( $fields, ['ST_Distance_Sphere(geom_sede, ST_MakePoint(:lon, :lat)) as distance'] ) )
        ->andWhere('ST_Distance_Sphere(geom_sede, ST_MakePoint(:lon, :lat)) <= :distance')
        ->addParams([
            ':lat' => floatval($params['lat']), 
            ':lon' => floatval($params['lon']), 
            ':distance' => intval($params['distance']*1000)
        ]);

        if(Yii::$app->request->get('id_comune')) $query->andWhere(['id_comune'=>$params['id_comune']]);
        if(Yii::$app->request->get('id_provincia')) $query->andWhere(['id_provincia'=>$params['id_provincia']]);
        if(Yii::$app->request->get('id_specializzazione_sede')) $query->andWhere(['id_specializzazione_sede'=>explode(",",$params['id_specializzazione_sede'])]);
        
        if(Yii::$app->request->get('id_organizzazione')) $query->andWhere(['id_organizzazione'=>$params['id_organizzazione']]);


        /**
         * è selezionata una categoria e non tipologia
         */
        if(Yii::$app->request->get('id_categoria') && !Yii::$app->request->get('id_tipologia')) :

            $cats = explode(",",$params['id_categoria']);
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

        //return $query->createCommand()->getRawSql();

        if(Yii::$app->request->get('id_tipologia')) :
            $aggregatori = UtlAggregatoreTipologie::find()
                            ->where(['id'=>explode(",",$params['id_tipologia'])])
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
                    'ref_tipo_id'=>explode(",",$params['id_utl_automezzo_tipo'])
                ],
                [
                    'tipologia_risorsa'=>'attrezzatura', 
                    'ref_tipo_id'=>explode(",",$params['id_utl_attrezzatura_tipo'])
                ]
            ]);
        elseif (Yii::$app->request->get('id_utl_automezzo_tipo') && !Yii::$app->request->get('id_utl_attrezzatura_tipo')) :
            // cerca un automezzo
            $query->andWhere([
                'tipologia_risorsa'=>'automezzo', 
                'ref_tipo_id'=>explode(",",$params['id_utl_automezzo_tipo']), 
            ]);
        elseif (!Yii::$app->request->get('id_utl_automezzo_tipo') && Yii::$app->request->get('id_utl_attrezzatura_tipo')) :
            // cerca una attrezzatura
            $query->andWhere([
                'tipologia_risorsa'=>'attrezzatura', 
                'ref_tipo_id'=>explode(",",$params['id_utl_attrezzatura_tipo']), 
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

        if(count($orders) > 0) $orders['distance'] = SORT_ASC;
        $query->orderBy($orders);
        
        $per_page = 30;
       
        
        $orgs = $query->limit(1000)->asArray()->all();

                
        $res = [];
        foreach ($orgs as $org) {            
            $res[] = array_merge( $org, 
                ['dist_km'=>(isset($sedi["".$org['id_sede']])) ? $sedi["".$org['id_sede']]['dist_km'] : $org['distance']/1000 ] );
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


    public function actionEventi() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $eventi = ViewEventi::find()->with(['sottoeventi','ingaggi']);
        

        if(Yii::$app->request->get('open')) {
            $eventi->where(['!=', 'stato', 'Chiuso']);
        } 

        $eventi = $eventi->all();
        
        $geo = [
            "type"=>"FeatureCollection",
            "features"=>[]
        ];

        foreach ($eventi as $el) {
            $geo["features"][] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates"=> [$el['lon'], $el['lat']],
                ],
                "properties" => $el
            ];
        }

        return $geo;
    }

    public function actionEventiSetLatLon($event_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $evento = UtlEvento::findOne( $event_id );
        
        $evento->lat = Yii::$app->request->post('lat');
        $evento->lon = Yii::$app->request->post('lon');
        $evento->is_public = ($evento->is_public) ? 1 : 0;
        
        if(!$evento->save()) {
            throw new Exception(json_encode($evento->getErrors()), 500);
        } else {
            return ['message'=>'ok'];
        }
    } 

    public function actionSegnalazioni() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $segnalazioni = ViewSegnalazioni::find()->with(['evento'])->all();
        $geo = [
            "type"=>"FeatureCollection",
            "features"=>[]
        ];

        foreach ($segnalazioni as $el) {
            $geo["features"][] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates"=> [$el['lon'], $el['lat']],
                ],
                "properties" => $el
            ];
        }

        return $geo;
    }

    public function actionRadio() {

        $geo = [
            "type"=>"FeatureCollection",
            "features"=>[]
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        $radios = \common\models\external\RadioMobili::find()->select('*')->all();
        foreach ($radios as $radio) {
            $geo["features"][] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates"=> [$radio['longitude'], $radio['latitude']],
                ],
                "properties" => $radio
            ];
        }

        return $geo;

    }

    public function actionElicotteri() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        
        $now = new \DateTime();
        $date = $now->format("Y-m-d");
        $end_time = $now->format("H:m:s");
        $start_time = $now->sub(new \DateInterval('P5M')); $now->format("H:m:s");
        $url = Yii::$app->params['elicopters']['host'];

        $geo = [
            "type"=>"FeatureCollection",
            "features"=>[]
        ];

        $guzzle_options = [
            'base_uri' => $url
        ];
        if(isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) :
            $guzzle_options['proxy'] = Yii::$app->params['proxyUrl'];
        endif;

        $client = new Client( $guzzle_options );
        $response = $client->request('GET', '/ws/eway/history.php', ['query' => 
            [
                'apikey' => Yii::$app->params['elicopters']['api_key'],
                'start_date' => $date, //"2018-07-29",//$date,
                'start_time' => $start_time,
                'end_date' => $date,
                'end_time' => $end_time,
                'format' => 'json',
                'events' => 'A' // event_name: "Posizione",
            ]
        ]);
        $res = json_decode($response->getBody()->getContents(), true);
        
        $array_elicotteri = [];
        if(!empty($res['rows'])) {

            
            foreach ($res['rows']['row'] as $position) {
                $time = \DateTime::createFromFormat( 'Y-m-d H:m:i', $position['local_timestamp'] );
                if ( isset($array_elicotteri[$position['device_id']]) ) {
                    $time_before = \DateTime::createFromFormat( 
                        'Y-m-d H:m:i', 
                        $array_elicotteri[$position['device_id']]['local_timestamp'] 
                    );

                    if( $time_before < $time ) {
                        $array_elicotteri[$position['device_id']] = $position;
                    }

                } else {
                    $array_elicotteri[$position['device_id']] = $position;
                }
            }
            
        } 
        
        foreach ($array_elicotteri as $key => $element) {
            $geo["features"][] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates"=> [ (float) $element['longitude'], (float) $element['latitude']],
                ],
                "properties" => $element
            ];
        }

        return $geo;
    }

    public $replace_useless = [
        'via', 'vi', 'pzza', 'piazza', 'pza', 'strada', 'strda', 'stada', 'strd', 'strad',
        'via ', 'vi ', 'pzza ', 'piazza ', 'pza ', 'strada ', 'strda ', 'stada ', 'strd ', 'strad '
    ];

    public function actionAddress (  ) 
    {

        $result_number = 15;
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        
        $string = Yii::$app->request->get('address');
        if(strlen($string) < 3) return ['too short'];


        $return_logs = [];
        $parsed = $this->removeUseless($string, $return_logs);
        // rimuovi i primi se è nello useless
        if(strlen($string) < 3) return ['too short'];
        $string = $parsed[0];
        $return_logs = $parsed[1];

        /**
         * New query
         * @var [type]
         */
        $rows = (new \yii\db\Query())
            ->select(['*'])
            ->from('_autocomplete_addresses')
            ->where('search_field @@ plainto_tsquery(:q)');

        if( Yii::$app->request->get('c') ) $rows->andWhere(['ilike','comune',Yii::$app->request->get('c').'%', false]);
        if( Yii::$app->request->get('pr') ) $rows->andWhere(['ilike','provincia',Yii::$app->request->get('c').'%', false]);

        $rows = $rows->addParams([
            'q' => Yii::$app->request->get('address')
        ])
        ->limit($result_number)
        ->all();

        return ['indirizzi',$rows];
        
        
    }

    private function removeUseless($address, $logs) {
        
        $str_length = strlen( $address );
        $replacements = [];
        foreach($this->replace_useless AS $value)
        {            
            if( preg_match( 
                '/^'.$value.'/', 
                substr($address, 0, 8) 
            ) > 0 ) {
                $string = str_replace($value, "", $address );
                $replacements[] = $string;
                $logs[] = "valore per " . $value . ": " .$string;
            }
            
        }
        
        if ( count( $replacements ) == 0 ) return [$address, $logs];

        $return_string = $replacements[0];
        // teoricamente il valore più lungo rimosso è quello che maggiormente fa il match con la stringa
        foreach ($replacements as $string_replaced) {
            if ( strlen( $string_replaced ) < strlen( $return_string ) ) $return_string = $string_replaced;
        }
        return [ trim ( $return_string ),$logs];
    }

    /**
     * Uso le query per determinare i componenti della stringa
     * @param  [type] $string [description]
     * @param  [type] $logs   [description]
     * @return [type]         [description]
     */
    private function useQuery ( $string, $logs )
    {

    }

}