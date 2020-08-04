<?php

namespace backend\controllers;

use common\models\ConEventoExtra;
use common\models\ConEventoSegnalazione;
use common\models\ConSegnalazioneExtra;
use common\models\ConUtenteExtra;
use common\models\LocComune;
use common\models\MyHelper;
use common\models\UtlEvento;
use common\models\UtlExtraSegnalazione;
use common\models\UtlExtraUtente;
use common\models\UtlOperatorePc;
use common\models\UtlSegnalazioneAttachments;
use common\models\UtlUtente;
use common\models\UtlAnagrafica;
use Exception;
use Yii;
use common\models\UtlSegnalazione;
use common\models\UtlSegnalazioneSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use common\models\UplMedia;
use common\models\UplTipoMedia;

use common\models\LocIndirizzo;
use common\models\LocCivico;

use common\models\ConEventoSegnalazioneApp;
/**
 * SegnalazioneController implements the CRUD actions for UtlSegnalazione model.
 */
class SegnalazioneController extends Controller
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
                        'actions' => ['index', 'lavorate', 'map', 'list-map', 'count'],
                        'permissions' => ['listSegnalazioni']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'permissions' => ['viewSegnalazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['close'],
                        'permissions' => ['closeSegnalazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['change-evento','attach-evento','approve-segnalazione-app','refuse-segnalazione-app'], // li consideriamo la stessa cosa
                        'permissions' => ['transformSegnalazioneToEvento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createSegnalazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateSegnalazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['removeSegnalazione']
                    ]
                ],
            ],
        ];
    }

    public function actionListMap() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return UtlSegnalazione::find()
                ->with('tipologia','utente.user', 'comune')
                ->where(['stato'=>'Nuova in lavorazione'])
                ->orderBy('dataora_segnalazione DESC')
                ->asArray()
                ->all();
    }

    /**
     * Segnalazioni da lavorare
     * @return [type] [description]
     */
    public function actionCount()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(!empty(Yii::$app->params['segnalazioni_da_lavorare']) && strtolower( Yii::$app->params['segnalazioni_da_lavorare'] ) == 'app' ) {
            return UtlSegnalazione::find()
            ->select(['id'])
            ->where(['stato'=>'Nuova in lavorazione'])
            ->andWhere(['fonte'=>'App'])
            ->all();
        } else {
            return UtlSegnalazione::find()->select(['id'])->where(['stato'=>'Nuova in lavorazione'])->all();
        }
    }

    /**
     * Lists all UtlSegnalazione models.
     * @return mixed
     */
    public function actionIndex()
    {
        $operatore = null;
        if (!Yii::$app->user->can('Funzionario')) {
            $operatore = UtlOperatorePc::find()->where(['iduser'=> Yii::$app->user->id])->one();
        }

        $searchModel = new UtlSegnalazioneSearch();
        $params = ['stato'=>['Nuova in lavorazione']];
        if(Yii::$app->user->can('gestioneIncendio',['tipologia' => '1'])){
            $operatore = null;
            $params = array_merge($params, ["tipologia" => '1']);
        }

        $data = array_merge(Yii::$app->request->queryParams, $params);
        $dataProvider = $searchModel->search($operatore, $data);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all UtlSegnalazione models.
     * @return mixed
     */
    public function actionLavorate()
    {
        $operatore = null;
        if (!Yii::$app->user->can('Funzionario')) {
            $operatore = UtlOperatorePc::find()->where(['iduser'=> Yii::$app->user->id])->one();
        }

        $searchModel = new UtlSegnalazioneSearch();
        $params = ['stato'=>['Verificata e trasformata in evento','Chiusa']];
        if(Yii::$app->user->can('gestioneIncendio',['tipologia' => '1'])){
            $operatore = null;
            $params = array_merge($params, ["tipologia" => '1']);
        }

        $data = array_merge(Yii::$app->request->queryParams, $params);
        $dataProvider = $searchModel->search($operatore, $data);

        return $this->render('lavorate', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Map all UtlSegnalazioni models.
     * @return mixed
     */
    public function actionMap()
    {
        return $this->render('map', [
        ]);
    }

    /**
     * Displays a single UtlSegnalazione model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if($model->tipologia) :
            

            $listEventi = UtlEvento::find()
            ->select( ['*', 'ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) as distance'] )
            ->where('ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) <= :distance')
            ->andWhere(['!=', 'stato', 'Chiuso'])
            ->addParams([
                ':lat' => floatval($model->lat), 
                ':lon' => floatval($model->lon), 
                ':distance' => intval(150000)
            ])
            ->orderBy(['distance'=>SORT_ASC])
            ->limit(300)
            ->all();
        else:
            $listEventi = [];
        endif;
        return $this->render('view', [
            'model' => $model,
            'listEventi' => $listEventi
        ]);
    }

    /**
     * Chiude una segnalazione impostando lo stato=3
     * @param in int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionClose($id)
    {

        $model = $this->findModel($id);
        $model->scenario = 'close';
        if ($model) {
            $model->stato = 'Chiusa';
            $model->save();
        }
        return $this->redirect(['index']);
    }

    /**
     * Change segnalazione in evento UtlSegnalazione model.
     * @param integer $id
     * @return mixed
     */
    public function actionChangeEvento($id)
    {
        $model = $this->findModel($id);
        $data = $model->attributes;
        unset($data['stato']);
        unset($data['num_protocollo']);

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
            $listEventi = UtlEvento::find()
            ->where(['tipologia_evento' => $model->tipologia->id])->orderBy('dataora_evento DESC')->limit(20)->all();
            return $this->render('view', [
                'model' => $this->findModel($id),
                'listEventi' => $listEventi
            ]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Attach  evento UtlSegnalazione model.
     * @param integer $id
     * @return mixed
     */
    public function actionAttachEvento($id, $idEvento)
    {
        $model = $this->findModel($id);

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

            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->redirect(['evento/view?id='.$idEvento]);
    }

    public function actionApproveSegnalazioneApp($id)
    {
        $model = $this->findModel($id);

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {


            $connection = $model->segnalazioneAppEvento;
            if(empty($connection)) throw new \Exception("Connessione non trovata", 404);

            $connection->confirmed = 1;
            if(!$connection->save()){
                throw new Exception('Errore salvataggio Connessione Segnalazione', 500);
            }

            // Creo connessione con Segnalazione
            $conEventoSegnalazione = new ConEventoSegnalazione();
            $conEventoSegnalazione->idsegnalazione = $id;
            $conEventoSegnalazione->idevento = $connection->id_evento;
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

            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->redirect(['evento/view?id='.$connection->id_evento]);
    }

    public function actionRefuseSegnalazioneApp($id)
    {
        $model = $this->findModel($id);

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {

            $connection = $model->segnalazioneAppEvento;
            if(empty($connection)) throw new \Exception("Connessione non trovata", 404);

            $connection->confirmed = 2;
            if(!$connection->save()){
                throw new Exception('Errore salvataggio Connessione Segnalazione', 500);
            }

            $dbTrans->commit();

        }catch (Exception $e) {
            $dbTrans->rollBack();

            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->redirect(['segnalazione/view?id='.$id]);
    }

    /**
     * Creates a new UtlSegnalazione model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlSegnalazione();
        $utente = new UtlUtente();
        $utente->scenario = 'createSegnalatore';

        //Variabile che mostra/nasconde i campi lat e lon nel form in caso di problemi di geolocalizzazione
        $showLatLon = false;

        if ($model->load( Yii::$app->request->post() )) {
            
            $model->stato = 'Nuova in lavorazione';
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            
            try {

                $params = Yii::$app->request->post('UtlSegnalazione');
                
                if( empty( $model->idcomune ) ) throw new Exception("Seleziona il comune");
                if( empty($model->lat) || empty($model->lon) ) throw new Exception("Inserisci le coordinate selezionando un indirizzo/toponimo o manualmente");

                $tel_params = Yii::$app->request->post('UtlSegnalazione');
                if( empty($tel_params['telefono_segnalatore']) )  
                {
                    throw new Exception("Inserisci il numero di telefono");
                }

                /*
                    No more dirty data in utl_anagrafica
                    $anagrafica = new UtlAnagrafica();
                    $anagrafica->load(Yii::$app->request->post());
                    $anagrafica->nome = @Yii::$app->request->post('UtlSegnalazione')['nome_segnalatore'];
                    $anagrafica->cognome = @Yii::$app->request->post('UtlSegnalazione')['cognome_segnalatore'];
                    $anagrafica->telefono = @Yii::$app->request->post('UtlSegnalazione')['telefono_segnalatore'];
                    $anagrafica->email = @Yii::$app->request->post('UtlSegnalazione')['email_segnalatore'];
                
                $anagrafica = $anagrafica->createOrUpdate();
                if($anagrafica->getErrors()) :
                    throw new Exception('Errore salvataggio dati Segnalatore. Controllare i dati', 500);
                endif;
                */
                //$utente->load(Yii::$app->request->post());
                //$utente->id_anagrafica = $anagrafica->id;
                // Salvataggio utente
                //if( !$utente->save(false) ){
                //    throw new Exception('Errore salvataggio Segnalatore. Controllare i dati', 500);
                //}

                // Aggiorno idutente e salvo il model
                //$model->idutente = $utente->getPrimaryKey();

                $segnalazione_address = Yii::$app->request->post('UtlSegnalazione')['address_type'];
                $post_segnalazione = Yii::$app->request->post('UtlSegnalazione');

                if( !\common\utils\GeometryFunctions::verifyLatLonInComune( $model->lat, $model->lon, $model->idcomune )) throw new Exception("Le coordinate inserite non rientrano nella geometria del comune selezionato");     

                switch($segnalazione_address) {
                    case 1:

                        if(empty($model->address) || empty($model->civico)) throw new \Exception("Inserisci indirizzo e seleziona il numero civico", 1);
                        // il cap è semi-virtuale, serve solo come check a posteriori per vedere se ha preso correttamente l'indirizzo
                        if(empty($model->cap)) throw new \Exception("L'indirizzo selezionato non è stato preso, riprova", 1);   
                        
                        $model->luogo = '';
                        $model->indirizzo = $model->address . ' ' . $model->civico . ' ' . $model->cap;

                    break;
                    case 2:
                        
                        if( empty($model->google_address) ) throw new Exception("Inserisci e seleziona l'indirizzo con l'autocomplete di google", 1);
                        
                        $model->indirizzo = '';
                        $model->luogo = $model->google_address;
                        
                    break;
                    case 3:
                        
                        if( empty($model->manual_address) ) throw new Exception("Inserisci l'indirizzo", 1);                        

                        $model->luogo = '';
                        $model->indirizzo = $model->manual_address;
                        
                    break;
                    case 4:

                        if( empty( $model->toponimo_address ) ) throw new Exception("Seleziona il toponimo dalla tendina", 1);
                        
                        $model->luogo = $model->toponimo_address;
                        $model->indirizzo = '';

                    break;
                }
                
                
                // Salvo idsalaoperativa prendendelo dall'operatore che effettua la segnalazione
                $operatore = UtlOperatorePc::find()->where(['iduser' => Yii::$app->user->id])->one();

                if(isset($operatore)){
                    $model->idsalaoperativa = $operatore->idsalaoperativa;
                }

                
                // Salvo il model segnalazione
                
                if(!$model->save()){
                    throw new Exception('Errore salvataggio Segnalazione. Controllare i dati', 500);
                }
                
                
                if(!empty(Yii::$app->request->post('UtlSegnalazione')['extras'])){

                    $extras = Yii::$app->request->post('UtlSegnalazione')['extras'];
                    $extrasInfoArray = !empty(Yii::$app->request->post('UtlSegnalazioneExtraInfo')) ? Yii::$app->request->post('UtlSegnalazioneExtraInfo') : null;
                    $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();

                    foreach ($mdExtras as $extra){
                        $model->link('extras', $extra);

                        $extraInfo = ConSegnalazioneExtra::find()->where(['idsegnalazione' => $model->id, 'idextra' => $extra->id])->one();

                        $data = [];
                        if(isset($extrasInfoArray[$extra->id])){
                            $data['ConSegnalazioneExtra'] = $extrasInfoArray[$extra->id];
                            $extraInfo->load($data);
                            $extraInfo->save();
                        }
                    }
                }

                // Salvo allegato segnalazione
                // get the uploaded file instance. for multiple file uploads
                $attachFile = UploadedFile::getInstance($model, 'attachment');

                if(!empty($attachFile)) {

                    $this->attachSegnalazioneAllegato( $attachFile, $model );

                    
                }


                $dbTrans->commit();

            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('error', $e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                    'utente' => $utente,
                    'showLatLon' => $showLatLon
                ]);
            }


            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'utente' => $utente,
                'showLatLon' => $showLatLon
            ]);
        }
    }

    /**
     * Updates an existing UtlSegnalazione model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $utente = UtlUtente::findOne($model->idutente);
        $utente->scenario = 'createSegnalatore';
        $anagrafica = $utente->getAnagrafica()->one();

        //Variabile che mostra/nasconde i campi lat e lon nel form in caso di problemi di geolocalizzazione
        $showLatLon = false;

        if ($model->load(Yii::$app->request->post())) {

            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {

                $params = Yii::$app->request->post('UtlSegnalazione');
                

                if( empty( $model->idcomune ) ) throw new Exception("Seleziona il comune");
                if( empty($model->lat) || empty($model->lon) ) throw new Exception("Inserisci le coordinate selezionando un indirizzo/toponimo o manualmente");

                if( empty($model->telefono_segnalatore) )  
                {
                    throw new Exception("Inserisci il numero di telefono");
                }

                
                if(!$utente->load(Yii::$app->request->post()) || !$utente->save(false)) {
                    throw new Exception('Errore salvataggio Segnalatore. Controllare i dati', 500);
                }

                $ana_params = Yii::$app->request->post('UtlAnagrafica');
                
                $ana_params['nome'] = @Yii::$app->request->post('UtlSegnalazione')['nome_segnalatore'];
                $ana_params['cognome'] = @Yii::$app->request->post('UtlSegnalazione')['cognome_segnalatore'];
                
                if($model->fonte != 'App') {
                    if(@Yii::$app->request->post('UtlSegnalazione')['telefono_segnalatore']) $ana_params['telefono'] = Yii::$app->request->post('UtlSegnalazione')['telefono_segnalatore'];
                    if(@Yii::$app->request->post('UtlSegnalazione')['email_segnalatore']) $ana_params['email'] = Yii::$app->request->post('UtlSegnalazione')['email_segnalatore'];
                    
                }

                if(!$anagrafica->load(['UtlAnagrafica'=>$ana_params]) || !$anagrafica->save(false)){
                    throw new Exception('Errore salvataggio dati anagrafici. Controllare i dati', 500);
                }


                $segnalazione_address = Yii::$app->request->post('UtlSegnalazione')['address_type'];
                $post_segnalazione = Yii::$app->request->post('UtlSegnalazione');

                if( !\common\utils\GeometryFunctions::verifyLatLonInComune( $model->lat, $model->lon, $model->idcomune )) throw new Exception("Le coordinate inserite non rientrano nella geometria del comune selezionato");         
                
                switch($segnalazione_address) {
                    case 1:

                        if(empty($model->address) || empty($model->civico)) throw new \Exception("Inserisci indirizzo e seleziona il numero civico", 1);
                        // il cap è semi-virtuale, serve solo come check a posteriori per vedere se ha preso correttamente l'indirizzo
                        if(empty($model->cap)) throw new \Exception("L'indirizzo selezionato non è stato preso, riprova", 1);   
                        
                        $model->luogo = '';
                        $model->indirizzo = $model->address . ' ' . $model->civico . ' ' . $model->cap;

                    break;
                    case 2:
                        
                        if( empty($model->google_address) ) throw new Exception("Inserisci e seleziona l'indirizzo con l'autocomplete di google", 1);
                        
                        $model->indirizzo = '';
                        $model->luogo = $model->google_address;
                        
                    break;
                    case 3:
                        
                        if( empty($model->manual_address) ) throw new Exception("Inserisci l'indirizzo", 1);                        

                        $model->luogo = '';
                        $model->indirizzo = $model->manual_address;
                        
                    break;
                    case 4:

                        if( empty( $model->toponimo_address ) ) throw new Exception("Seleziona il toponimo dalla tendina", 1);
                        
                        $model->luogo = $model->toponimo_address;
                        $model->indirizzo = '';

                    break;
                }

                
                // Model Save
                if(!$model->save()){
                    throw new Exception('Errore salvataggio Segnalazione. Controllare i dati', 500);
                }

                // Aggiorno gli extra
                ConSegnalazioneExtra::deleteAll(['idsegnalazione' => $model->id]);
                if(!empty(Yii::$app->request->post('UtlSegnalazione')['extras'])){
                    $extras = Yii::$app->request->post('UtlSegnalazione')['extras'];
                }
                $extrasInfoArray = Yii::$app->request->post('UtlSegnalazioneExtraInfo');
                
                if(!empty($extras)) {
                    $mdExtras = UtlExtraSegnalazione::find()->where(['id' => $extras])->all();
                    foreach ($mdExtras as $extra) {
                        $model->link('extras', $extra);
                        $extraInfo = ConSegnalazioneExtra::find()->where(['idsegnalazione' => $model->id, 'idextra' => $extra->id])->one();

                        $data = [];
                        if (isset($extrasInfoArray[$extra->id])) {
                            $data['ConSegnalazioneExtra'] = $extrasInfoArray[$extra->id];
                            $extraInfo->load($data);
                            $extraInfo->save();
                        }
                    }
                }

                // Salvo allegato segnalazione
                // get the uploaded file instance. for multiple file uploads
                $attachFile = UploadedFile::getInstance($model, 'attachment');

                if(!empty($attachFile)){

                    $this->attachSegnalazioneAllegato( $attachFile, $model );
                    
                }


                $dbTrans->commit();

            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('error', $e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                    'utente' => $utente,
                    'showLatLon' => $showLatLon
                ]);
            }


            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'utente' => $utente,
                'showLatLon' => $showLatLon
            ]);
        }
    }

    /**
     * Deletes an existing UtlSegnalazione model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UtlSegnalazione model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlSegnalazione the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlSegnalazione::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Inserisci allegato segnalazione in upl_media e linkalo
     * @param  [type] $file         [description]
     * @param  [type] $segnalazione [description]
     * @return [type]               [description]
     */
    protected function attachSegnalazioneAllegato( $file, $segnalazione ) {
        $tipo = UplTipoMedia::find()->where(['descrizione'=>'Allegato segnalazione'])->one();
        if( empty($tipo) ) {
            $tipo = new UplTipoMedia;
            $tipo->descrizione = 'Allegato segnalazione';
            $tipo->save();
        }

        $valid_files = ['application/pdf'];
        
        $media = new UplMedia;
        $media->uploadFile($file, $tipo->id, $valid_files, "File non valido, inserisci un pdf");
        $media->refresh();

        $segnalazione->link('media', $media);

    }
}
