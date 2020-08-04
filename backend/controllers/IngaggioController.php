<?php

namespace backend\controllers;

use common\models\ConOperatoreTask;
use Yii;
use common\models\UtlIngaggio;
use common\models\UtlIngaggioSearch;
use common\models\ConVolontarioIngaggioSearch;

use common\models\VolVolontario;
use common\models\VolVolontarioSearch;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\Response;
use common\models\UtlIngaggioSearchForm;

use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzo;
use common\models\VolSede;

use yii\data\ActiveDataProvider;

use common\models\UtlAnagrafica;
use common\models\ConVolontarioIngaggio;
use common\models\UtlAggregatoreTipologie;
use yii\helpers\ArrayHelper;
/**
 * IngaggioController implements the CRUD actions for UtlIngaggio model.
 */
class IngaggioController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'search-organizzazione' => ['GET'],
                    'ingaggia' => ['GET']
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user){
                        Yii::error(json_encode( Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId()) ));
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['ingaggia', 'search-organizzazione', 'distance', 'create'],
                        'permissions' => ['createIngaggio']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-volontario', 'update', 'view', 'update-volontario', 'delete-volontario'],
                        'permissions' => ['updateIngaggio']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'change-data-chiusura'],
                        'permissions' => ['adminPermissions']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['change-data'],
                        'permissions' => ['adminPermissions', 'changeDateAttivazioni']
                    ]
                ],

            ],
        ];
    }

    /**
     * Lists all UtlIngaggio models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlIngaggioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAddVolontario($id_ingaggio)
    {
        $ingaggio = $id_ingaggio;

        $model = UtlIngaggio::findOne($id_ingaggio);

        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;
        
        $datas = Yii::$app->request->post('ConVolontarioIngaggio');
        
        if( isset($datas['id_volontario']) && $datas['id_volontario'] != "" ) : 

            $volontario = VolVolontario::findOne($datas['id_volontario']);

            $associate = new ConVolontarioIngaggio();
            $associate->refund = $datas['refund'];
            $associate->id_ingaggio = $id_ingaggio;
            $associate->id_volontario = $datas['id_volontario'];
            $associate->datore_di_lavoro = !empty($volontario->datore_di_lavoro) ? $volontario->datore_di_lavoro : null;

            if(!$associate->save()) {
                Yii::$app->response->statusCode = 422;
                return $associate->getErrors();
            } 

            return ['message'=>'ok'];
            
        else:
            
            $anagrafica = new UtlAnagrafica();
            $anagrafica->nome = $datas['nome'];
            $anagrafica->cognome = $datas['cognome'];
            
            $anagrafica->email = $datas['email'];
            $anagrafica->telefono = $datas['telefono'];
            $anagrafica = $anagrafica->createOrUpdate();
            if($anagrafica->getErrors()) :
                Yii::$app->response->statusCode = 422;
                return $anagrafica->getErrors();
            endif;

            $existVolontario = VolVolontario::find()
            ->where(['id_anagrafica'=>$anagrafica->id])
            ->andWhere(['id_organizzazione'=>$model->idorganizzazione])
            ->one();

            $volontario = ($existVolontario) ? $existVolontario : new VolVolontario();
            $volontario->id_anagrafica = $anagrafica->id;
            $volontario->id_organizzazione = $model->idorganizzazione;
            $volontario->id_sede = $model->idsede;

            if(!$volontario->save()) :
                Yii::$app->response->statusCode = 422;
                return $anagrafica->getErrors();
            endif;

            $associate = new ConVolontarioIngaggio();
            $associate->id_volontario = $volontario->id;
            $associate->id_ingaggio = $id_ingaggio;
            $associate->refund = $datas['refund'];
            $associate->save();

            return ['message'=>'ok'];
        endif;

        
    }

    /**
     * Aggiorna il refund di un volontario
     * @param  [type] $id_ingaggio_volontario [description]
     * @return [type]                         [description]
     */
    public function actionUpdateVolontario($id_ingaggio_volontario)
    {
        $conn = ConVolontarioIngaggio::findOne($id_ingaggio_volontario);
        if(!$conn) throw new \Exception("Connessione non trovata", 1);
        

        $conn->refund = !$conn->refund;
        if(!$conn->save()) throw new \Exception("Errore in aggiornamento", 1);
        

        return $this->redirect(['ingaggio/update', 'id'=>$conn->id_ingaggio]);
    }

    /**
     * Rimuovi un volontario dall'attivazione
     * @param  [type] $id_ingaggio_volontario [description]
     * @return [type]                         [description]
     */
    public function actionDeleteVolontario($id_ingaggio_volontario)
    {
        $conn = ConVolontarioIngaggio::findOne($id_ingaggio_volontario);
        if(!$conn) throw new \Exception("Connessione non trovata", 1);
        
        $id_ingaggio = $conn->id_ingaggio;

        $conn->delete();

        return $this->redirect(['ingaggio/update', 'id'=>$id_ingaggio]);
    }

    /**
     * Displays a single UtlIngaggio model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $params = ( isset(Yii::$app->request->queryParams['ConVolontarioIngaggioSearch']) ) ? 
        ['ConVolontarioIngaggioSearch'=> array_merge(Yii::$app->request->queryParams['ConVolontarioIngaggioSearch'],['id_ingaggio'=>$id])] : 
        ['ConVolontarioIngaggioSearch'=>['id_ingaggio'=>$id]];

        $searchModel = new ConVolontarioIngaggioSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new UtlIngaggio model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlIngaggio();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UtlIngaggio model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $evento = $model->getEvento()->one();
        if( $evento->stato == 'Chiuso' ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(Yii::$app->request->method == 'POST') {

            $post_data = Yii::$app->request->post();

            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {

                $model->load($post_data);
                if(!$model->save()) {
                    throw new \Exception("Errore " . json_encode($model->getErrors()), 1);
                }

                $dbTrans->commit();
                return $this->redirect(['view', 'id' => $model->id]);

            } catch(\Exception $e) {
                $dbTrans->rollBack();
                throw $e;
            }

        }


        $params = ( isset(Yii::$app->request->queryParams['ConVolontarioIngaggioSearch']) ) ? 
        ['ConVolontarioIngaggioSearch'=> array_merge(Yii::$app->request->queryParams['ConVolontarioIngaggioSearch'],['id_ingaggio'=>$id])] : 
        ['ConVolontarioIngaggioSearch'=>['id_ingaggio'=>$id]];



        $searchModel = new ConVolontarioIngaggioSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }


    /**
     * Modifica date
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionChangeData($id)
    {
        $model = $this->findModel($id);
        
        $evento = $model->getEvento()->one();
        if( $evento->stato == 'Chiuso' ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(Yii::$app->request->method == 'POST') {

            $post_data = Yii::$app->request->post();

            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                
                if(!empty($post_data['UtlIngaggio']['closed_at'])) {
                    $closed_at = \DateTime::createFromFormat('d-m-Y H:i', $post_data['UtlIngaggio']['closed_at']);
                    if(is_bool($closed_at)) throw new \Exception("Data di chiusura non valida", 1);
                    $post_data['UtlIngaggio']['closed_at'] = $closed_at->format('Y-m-d H:i');
                }

                if(!empty($post_data['UtlIngaggio']['created_at'])) {
                    $created_at = \DateTime::createFromFormat('d-m-Y H:i', $post_data['UtlIngaggio']['created_at']);
                    if(is_bool($created_at)) throw new \Exception("Data di apertura non valida", 1);
                    $post_data['UtlIngaggio']['created_at'] = $created_at->format('Y-m-d H:i');
                }

                $model->load($post_data);
                if(!$model->save()) {
                    throw new \Exception("Errore " . json_encode($model->getErrors()), 1);
                }


                $dbTrans->commit();
                return $this->redirect(['view', 'id' => $model->id]);

            } catch(\Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage() );
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }


        $params = ( isset(Yii::$app->request->queryParams['ConVolontarioIngaggioSearch']) ) ? 
        ['ConVolontarioIngaggioSearch'=> array_merge(Yii::$app->request->queryParams['ConVolontarioIngaggioSearch'],['id_ingaggio'=>$id])] : 
        ['ConVolontarioIngaggioSearch'=>['id_ingaggio'=>$id]];



        $searchModel = new ConVolontarioIngaggioSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    

    /**
     * Deletes an existing UtlIngaggio model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $idevento = $model->idevento;
        $model->delete();

        return (!empty($idevento)) ? 
            $this->redirect(['evento/view', 'id'=>$idevento])
            : $this->redirect(['evento/index']);
    }

    /**
     * Finds the UtlIngaggio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlIngaggio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlIngaggio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionSearchOrganizzazione()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $params = Yii::$app->request->get();

        
        /**
         * Implementiamo la ricerca per distanza anche qui
         */
        $connection = Yii::$app->getDb();


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
                        ORDER BY (vert.the_geom::geography <-> (SELECT geom FROM point_selected)::geography) LIMIT 1 
                    ), 
                    ARRAY( SELECT nvf.id_vert FROM view_routing_organizzazioni nvf ), 
                    false)
                 dc(start_vid, end_vid, agg_cost) 
            INNER JOIN view_routing_organizzazioni near ON near.id_vert = dc.end_vid 
            INNER JOIN view_organizzazioni f ON f.id_sede = near.id_sede WHERE 
            tipologia_risorsa='sede'
            ORDER BY n_ord ASC LIMIT 10000 ) AS \"vtable\"
            ")
        ->bindValue ( ':lat', floatval($params['lat']) )
        ->bindValue ( ':lon', floatval($params['lon']) );
            
            
        $result = $command->queryAll();
        $sedi = [];
        foreach ($result as $r) {
            $sedi[$r['id_sede']] = $r;
        }
        
        
        if(!Yii::$app->request->get('distance')) $params['distance'] = 200;

        
        $fields = [
            'DISTINCT(CONCAT( ref_id, \'_\', tipologia_risorsa ) ) riferimento',
            'codice_associazione',
            'ref_id',
            'ref_identifier',
            'ref_meta',
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
            'ambito'
        ];
        $group = [
            'codice_associazione',
            'ref_id',
            'ref_identifier',
            'ref_meta',
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
            'ambito',
            'geom_sede'
        ];
        $query = UtlIngaggioSearchForm::find()
        ->select( $fields )
        ->with(['sezioneSpecialistica', 'contattiAttivazioni', 'contattiAttivazioni.contatto'])
        ->groupBy($group);

        
        if(Yii::$app->request->get('num_comunale')) $query->andWhere(['num_comunale'=>$params['num_comunale']]);
        if(Yii::$app->request->get('id_comune')) $query->andWhere(['id_comune'=>$params['id_comune']]);
        if(Yii::$app->request->get('id_provincia')) $query->andWhere(['id_provincia'=>$params['id_provincia']]);

        /**
         * Qui va implementata una subquery
         */
        if(Yii::$app->request->get('specializzazioni')) {
            $specs = explode(",", Yii::$app->request->get('specializzazioni'));
            $query->joinWith('sezioneSpecialistica')
            ->andWhere(['tbl_sezione_specialistica.id' => $specs]);
            
        }
        
        
        if(Yii::$app->request->get('id_organizzazione')) $query->andWhere(['id_organizzazione'=>$params['id_organizzazione']]);

        /**
         * è selezionata una categoria e non tipologia
         */
        if(Yii::$app->request->get('id_categoria') && (!Yii::$app->request->get('id_tipologia') || Yii::$app->request->get('id_tipologia') == '') ) :
            $cats = explode(",", Yii::$app->request->get('id_categoria'));
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
            endif; // categoria tutti

        endif;

        
        if(Yii::$app->request->get('id_tipologia') && Yii::$app->request->get('id_tipologia') != '') :
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
        $sort_order = Yii::$app->request->get('sort_order') && Yii::$app->request->get('sort_order') == 'asc' ? SORT_ASC : SORT_DESC;
        
        $orders = [];
        
        if(Yii::$app->request->get('sort')) :
            $sort_map = UtlIngaggioSearchForm::sortModelMap();
            if ( isset( $sort_map[ Yii::$app->request->get('sort') ] ) ) :
                foreach ( $sort_map[ Yii::$app->request->get('sort') ] as $value ) :
                    $orders[$value] = $sort_order;
                endforeach;
            endif;            
        endif;

        
        if(!empty($orders)) $query->orderBy($orders);
        
       
        
        $orgs = $query->asArray()->all();

        $res = [];
        $num = count($orgs);
        foreach ($orgs as $org) {            
            $res[] = array_merge( $org, 
                ['distance'=>(isset($sedi["".$org['id_sede']])) ? $sedi["".$org['id_sede']]['dist_km'] : 999999 ] );
        }


        ArrayHelper::multisort($res, ['distance'], SORT_ASC);
        $page = (!empty(Yii::$app->request->get('page'))) ? intval(Yii::$app->request->get('page')) : 1;
        return [
            'data' => array_slice( $res, ($page-1)*15, 15 ),//$orgs,
            'total' => $num,
            'page' => $page,
            'pages' => ($num % 15 == 0) ? $num/15 : intval($num/15) +1
        ];
    }

    /**
     * Attiva una risorsa
     * @return [type] [description]
     */
    public function actionIngaggia() 
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        
        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        try {
        
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

            if(!$ingaggio->save()){
                return ['error'=>$ingaggio->getErrors()];
            }

            $task_name = 'Richiesta nuova attivazione - '. Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');
            if(Yii::$app->request->get('ref_type') == 'automezzo' && $obj && $obj->targa) :
                $task_name .= " - " . $obj->targa . " -";
            endif;
            //Salvo dati giornale evento
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
            $diarioEvento->idtask = 4; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $ingaggio->idevento;
            $diarioEvento->note = $task_name.' '. $ingaggio->organizzazione->ref_id . " " . $ingaggio->organizzazione->denominazione ;
            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

            if(!($diarioEvento->save())){
                return ['error'=>$diarioEvento->getErrors()];
            }

            $dbTrans->commit();

        } catch (\Exception $e) {
            
            $dbTrans->rollBack();
            throw $e;
        }



        return $ingaggio;
    }


    /**
     * Distanza data da api google 
     * @deprecated
     * @return [type] [description]
     */
    public function actionDistance()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $str = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".
        Yii::$app->request->get('from_lat').
        ",".
        Yii::$app->request->get('from_lon').
        "&destinations=".
        Yii::$app->request->get('to_lat').
        ",".
        Yii::$app->request->get('to_lon').
        "&language=it-IT&key=".
        Yii::$app->params['google_key'];

        if(isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) :

            $proxy = Yii::$app->params['proxyUrl'];

            $context = array(
                'http' => array(
                    'proxy' => $proxy,
                    'request_fulluri' => True,
                    ),
                );

            $context = stream_context_create($context);
            $res = file_get_contents($str, false, $context);
        else:
            try {
                $res = file_get_contents($str);
            } catch (\Exception $e) {
                // Handle exception
                return false;
            }

        endif;
        
        $decoded = json_decode($res, true);

        $result = ['error'=>'Not found'];
        if(isset($decoded['rows'])) {
            foreach ($decoded['rows'] as $el) {
                if(isset($el['elements'])){
                    foreach ($el['elements'] as $element) {
                        if(isset($element['duration'])){
                            $result = [
                                'distance' => $element['distance']['text'],
                                'duration' => $element['duration']['text']
                            ];
                        }
                    }
                }
            }
        }
        return $result;



    }


}
