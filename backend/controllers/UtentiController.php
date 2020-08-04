<?php

namespace backend\controllers;


use common\models\User;
use common\models\UtlAnagrafica;
use Exception;
use Yii;
use common\models\UtlUtente;
use common\models\UtlUtenteSearch;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\base\Security;

use common\models\MasRubrica;
use common\models\utility\UtlIndirizzo;
/**
 * UtentiController implements the CRUD actions for UtlUtente model.
 */
class UtentiController extends Controller
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
                    'index' => ['POST', 'GET']
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user){
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['listAppUser']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createAppUser']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'update-sms-status', 'attiva','send-sms','send-push','attiva','disattiva'],
                        'permissions' => ['updateAppUser']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteAppUser']
                    ],                    
                ]
            ],
        ];
    }

    /**
     * Lists all UtlUtente models.
     * @return mixed
     */
    public function actionIndex()
    {

        $params = Yii::$app->request->queryParams;

        //Update sms_status in the last $params days only in first index page
        //if(empty($params)){
            //$this->actionUpdateSmsStatus(10);
        //}
        
        $searchModel = new UtlUtenteSearch();
        $dataProvider = $searchModel->search($params);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UtlUtente model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UtlUtente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlUtente();
        $model->scenario = 'createUtente';

        $anagrafica = new UtlAnagrafica();
        $anagrafica->scenario = UtlAnagrafica::SCENARIO_UTL_UTENTE;
        $rubrica = new MasRubrica();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }


        if ($anagrafica->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {

            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {

                $anagrafica = $anagrafica->createOrUpdate();
                // Create utente
                if($anagrafica->getErrors()){
                    throw new Exception('Errore salvataggio utente. Controllare i dati '.json_encode($anagrafica->getErrors()), 500);
                }

                $model->id_anagrafica = $anagrafica->id;
                $model->tipo = 2; // ente pubblico default
                $model->codice_attivazione = strtoupper(  (new Security)->generateRandomString( 8 ) );

                /**
                 * Crea un record custom per la rubrica
                 * @var [type]
                 */
                $rubrica = MasRubrica::find()->where(['id_anagrafica'=>$anagrafica->id])->one();
                if(!$rubrica) {
                    
                    $indirizzo = new UtlIndirizzo;
                    $indirizzo->id_comune = Yii::$app->request->post('UtlAnagrafica')['comune_residenza'];
                    if(!$indirizzo->save()) throw new Exception('Errore salvataggio utente. Controllare i dati '.json_encode($indirizzo->getErrors()), 500);

                    $rubrica = new MasRubrica;
                    $rubrica->load(Yii::$app->request->post());
                    $rubrica->id_anagrafica = $anagrafica->id;
                    $rubrica->id_indirizzo = $indirizzo->id;
                    if(!$rubrica->save()) throw new Exception('Errore salvataggio utente. Controllare i dati '.json_encode($rubrica->getErrors()), 500);

                } else {
                    if(Yii::$app->request->post('UtlAnagrafica')['comune_residenza'] != $rubrica->indirizzo->id_comune ) {
                        $rubrica->indirizzo->id_comune = Yii::$app->request->post('UtlAnagrafica')['comune_residenza'];
                        if(!$rubrica->indirizzo->save()) throw new Exception('Errore salvataggio utente. Controllare i dati '.json_encode($rubrica->indirizzo->getErrors()), 500);
                    }
                }



                /**
                 * Volutamente non inseriamo indirizzo perchè necesario solo in rubrica e appartenente a questa
                 */

                // Create utente
                if(!$model->save()){
                    throw new Exception('Errore salvataggio utente. Controllare i dati '.json_encode($model->getErrors()), 500);
                }



                $dbTrans->commit();

            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UtlUtente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); 

        // solo ente pubblico
        if($model->tipo != 2) return $this->redirect(['view', 'id' => $model->id]);

        $model->scenario = 'updateUtente';
        $anagrafica = $model->anagrafica;
        $rubrica = $model->rubrica;


        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($anagrafica->load(Yii::$app->request->post()) && 
            $model->load(Yii::$app->request->post()) && 
            $rubrica->load(Yii::$app->request->post())
        ) {

            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                
                // Create utente
                if(!$anagrafica->save()){
                    throw new Exception('Errore salvataggio utente. Controllare i dati', 500);
                }

                // Salvo utente
                if(!$model->save()){
                    throw new Exception('Errore modifica dati Utente. Controllare i dati'.json_encode($model->getErrors()), 500);
                }

                // Salvo rubrica
                if(!$rubrica->save()){
                    throw new Exception('Errore modifica dati Utente. Controllare i dati', 500);
                }

                // Salvo indirizzo
                $rubrica->indirizzo->id_comune = $anagrafica->comune_residenza;
                if(!$rubrica->indirizzo->save()){
                    throw new Exception('Errore modifica dati Utente. Controllare i dati', 500);
                }

                
                $dbTrans->commit();
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('update', [
                    'model' => $model
                ]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UtlUtente model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * Il metodo elimina dalla tabella user anche l'eventuale record associato
     * @param in integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {

            $mdUtente = $this->findModel($id);
            $mdUser = $mdUtente->user;
            if ($mdUser) $mdUser->delete();
            if( $mdUtente->rubrica ) {
                
                if($mdUtente->rubrica->indirizzo) $mdUtente->rubrica->indirizzo->delete();

                $mdUtente->rubrica->delete();
            }
            $mdUtente->delete();
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }


    /**
     * Update sms status index view.
     * @return mixed
     * @deprecated
     */
    public function actionUpdateSmsStatus($days){

        // Cerco tutti gli utenti con sms_status nullo
        $dateCheck = (string) strtotime("-{$days} days");

        $utenti = UtlUtente::find()
            ->joinWith('user')
            ->andWhere(['sms_status' => null])
            ->andWhere(['>=', 'user.created_at', $dateCheck])
            ->all();

        foreach ($utenti as $index => $utente){

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                $curl = curl_init();
                $telefono = preg_replace("/[^0-9]/", '', $utente->telefono);

                if(strlen($telefono) >= 9){
                    $curl = false;
                    // rimosso curl con dati accesso
                    $response = curl_exec($curl);
                    $responseJson = json_decode($response,true);
                    if (isset($responseJson['code']) && $responseJson['code']==400) {
                        throw new Exception('Errore invio SMS. Controllare i dati', 500);
                    }
                }

                if(!empty($responseJson['resources'][0]['id'])){

                    // Aggiorno model UtlUtente
                    $utente->sms_status = $responseJson['resources'][0]['status'];
                    $utente->save(false);

                    Yii::warning("\nAggiornamento utenti OK ".$index.": ".$telefono. "-----". $responseJson['resources'][0]['status']);
                }else{
                    Yii::warning( "\n Aggiorntamento utenti KO  ".$index.": ".$telefono);
                }

                $transaction->commit();

            } catch (Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
                exit;
            }
        }
    }

    /**
     * Page send push.
     * If send message is successful, the browser will be redirected to the page.
     * @return mixed
     * @deprecated
     */
    public function actionSendPush()
    {
        $model = new \yii\base\DynamicModel(['message']);
        $model->addRule(['message'], 'required')->addRule('message', 'string',['max'=>255]);

        if($model->load(Yii::$app->request->post())){

            $query = UtlUtente::find()->select('device_token')->where(['not', ['device_token' => null]])->andWhere(['device_vendor' => 'android']);

            // or to iterate the row one by one
            $push_android_tokens = [];
            foreach ($query->each() as $utente) {
                $push_android_tokens[] = $utente->device_token;
            }
            $message = $model->message;

            /* @var $apnsGcm \bryglen\apnsgcm\ApnsGcm */
            $apnsGcm = Yii::$app->apnsGcm;
            $apnsGcm->sendMulti(\bryglen\apnsgcm\ApnsGcm::TYPE_GCM, $push_android_tokens, $message,
                [
                    'customerProperty' => 1,
                    'title' => 'EasyAlert - Allerta Meteo',
                    'sound' => 'default',
                    'badge' => '1',
                    'icon' => 'notification_icon'
                ],
                [
                    'timeToLive' => 3,
                ]
            );

            if(count($apnsGcm->errors) != 0){
                Yii::$app->session->setFlash('error', $apnsGcm->errors[0]);
            }else{
                Yii::$app->session->setFlash('success', 'Messaggio inviato correttamente');
            }

            // do somenthing with model

            $model->message = '';
            return $this->render('send-push', [
                'model' => $model,
            ]);
        }

        return $this->render('send-push', ['model'=>$model]);
    }

    /**
     * Send new sms code to UtlUtente model.
     * If the send process is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @deprecated
     */
    public function actionSendsms($id)
    {
        $utente =  UtlUtente::findOne($id);
        $smsCode = $utente->smscode;

        if(!empty($smsCode)) {

            // USA IL SERVIZIO TEXT-MAGIC-SMS
            $curl = curl_init();
            //$telefono = str_replace(".","",$utente->telefono);
            $telefono = preg_replace("/[^0-9]/", '', $utente->telefono);
            //$telefono = str_replace("/[^0-9]/", '', '3403890664');

            curl_setopt_array($curl, array(
                // rimosse credenziali
            ));
            $response = curl_exec($curl);
            $responseJson = json_encode($response);
            if (isset($responseJson['code']) && $responseJson['code'] == 400) {
                Yii::$app->session->setFlash('error', 'Errore invio SMS. Controllare i dati');
            }

            Yii::$app->session->setFlash('success', 'Codice SMS inviato correttamente. Attendere qualche secondo per verificare lo stato dell\'invio');

        }else{
            Yii::$app->session->setFlash('error', 'Nessun codice SMS associato all\'utente selezionato.');
        }

        return $this->redirect('index');

    }

    /**
     * Change status User related to UtlUtente model.
     * If the send process is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @deprecated
     */
    public function OldActionAttiva($id)
    {
        $utente =  UtlUtente::findOne($id);
        $user = User::find()->where(['id' => $utente->iduser])->one();

        if(!empty($user)) {

            $user->status = User::STATUS_ACTIVE;
            $user->save(false);

            Yii::$app->session->setFlash('success', 'Utente attivato correttamente');
        }else{
            Yii::$app->session->setFlash('error', 'Impossibile attivare l\'utente, nessuno profilo associato');
        }

        return $this->redirect('index');
    }

    /**
     * Imposta utl_utente->enabled = 1
     * @return mixed
     * 
     */
    public function actionAttiva($id)
    {
        $utente =  UtlUtente::findOne($id);
        $utente->scenario = 'attivaDisattiva';
        $utente->enabled = 1;
        if(!$utente->save()) {
            Yii::$app->session->setFlash('error', 'Errore, non è stato possibile modificare lo stato');
        } else {
            Yii::$app->session->setFlash('success', 'Utente disabilitato correttamente');
        }   
                
        return $this->redirect('index');
    }

    /**
     * Imposta utl_utente->enabled = 0
     * @return mixed
     * @deprecated
     */
    public function actionDisattiva($id)
    {

        $utente =  UtlUtente::findOne($id);
        $utente->scenario = 'attivaDisattiva';
        $utente->enabled = 0;
        if(!$utente->save()) {
            Yii::$app->session->setFlash('error', 'Errore, non è stato possibile modificare lo stato');
        } else {
            Yii::$app->session->setFlash('success', 'Utente disabilitato correttamente');
        }        
        
        return $this->redirect('index');
    }

    /**
     * Finds the UtlUtente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlUtente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlUtente::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
