<?php // 9958
namespace api\modules\v1\controllers;

use api\modules\v1\models\AppSignup;
use common\models\UtlAnagrafica;
use common\models\utility\UtlContatto;
use common\models\UtlUtente;
use common\models\VolOrganizzazione;
use common\models\VolVolontario;
use Exception;
use Yii;
use yii\base\Controller;
use yii\data\ActiveDataProvider;
use yii\data\ActiveDataFilter;

use sizeg\jwt\JwtHttpBearerAuth;
use sizeg\jwt\Jwt;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use common\models\UtlOperatorePc;
use common\models\LoginForm;
use common\models\User;

use common\models\MasRubrica;

use api\utils\SendMail;
use backend\models\ResetPasswordForm;

use api\utils\ResponseError;

/**
 * Auth Controller
 *
 * Autenticazione da app
 */
class AuthController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'except' => ['login','options', 'create', 'confirm','reset','recovery']
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    /**
     * Di default per il metodo options torniamo ok in modo da non avere errori not found dalle chiamate automatiche del browser
     * @return [type] [description]
     */
    public function actionOptions() {
        return ['message'=>'ok'];
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        $params = Yii::$app->getRequest()->getBodyParams();

        /**
         * Il login dall'app viene fatto con la mail
         * per compatibilità operatori di sala prendo username corrispondente alla mail
         */
        if(!empty($params['username'])) {
            $user = User::find()->where(['email'=>$params['username']])->one();
            if(!$user) ResponseError::returnSingleError( 404, "Credenziali errate");

            $utente = UtlUtente::findOne(['iduser'=>$user->id]);
            if(!$utente) ResponseError::returnSingleError( 422, "Non sei abilitato all'uso dell'app");

            if($utente->tipo == 3) {
                $count = VolVolontario::find()->where(['id_anagrafica'=>$utente->id_anagrafica])->andWhere(['operativo'=>true])->count();
                if($count <= 0) {
                    ResponseError::returnSingleError(403, "Non sei autorizzato ad accedere non essendo più un volontario operativo");
                }
            }

            $params['username'] = $user->username;
        }

        if ($model->load($params, '') && $model->login()) :

            $request = new yii\web\Request;
            $ip = $request->getUserIP();

            $agent = $request->getUserAgent();

            $signer = new Sha256();

            $token = Yii::$app->jwt->getBuilder()
            ->setIssuer(Yii::$app->params['iss']) 
            ->setAudience(Yii::$app->params['aud']) 
            ->setId(Yii::$app->params['tid'], true) 
            ->setIssuedAt(time()) 
            ->setNotBefore(time()) 
            ->setExpiration(time() + (3600*24*365)) 
            ->set( 'uid', Yii::$app->user->identity->id ) 
            ->set( 'ip', $ip )
            ->set( 'agent', $agent )
            ->sign($signer, Yii::$app->params['secret-key'])
            ->getToken(); 
            
            $utente = UtlUtente::findOne(['iduser'=>Yii::$app->user->identity->id]);
            if(!$utente) ResponseError::returnSingleError( 422, 'Utente app non trovato' );

            $anagrafica = UtlAnagrafica::find()->where(['id'=>$utente->id_anagrafica])->one();
            if(!$anagrafica) ResponseError::returnSingleError( 422, 'Dati utente app non trovati' );

                      
            if(!empty($params['device_token']) && !empty($params['device_vendor']) && !empty($params['device_id'])){                

                Yii::error('Aggiorno token ' . $params['device_id'], 'api');

                $to_link = false;
                $contatto = UtlContatto::find()->where(['note'=>$params['device_id']])->one();
                if(!$contatto) {
                    // se non avevamo il contatto lo creiamo
                    $to_link = true;
                    $contatto = new UtlContatto;
                } else {
                    // verifichiamo che non sia di un vecchio contatto
                    $old_anagrafica = $contatto->getAnagrafica()->one();
                    if($old_anagrafica) {
                        // in quel caso eliminiamo il vecchio
                        if($old_anagrafica->id != $anagrafica->id) {
                            $old_anagrafica->unlink('contatto', $contatto, true);
                            // e impostiamo per mettere il nuovo
                            $to_link = true;
                        } else {
                            Yii::error('no old anagrafica ' . $params['device_id'], 'api');
                        }
                    } else {
                        Yii::error('no old anagrafica ' . $params['device_id'], 'api');
                    }
                }


                /**
                 * Salvo il device_id nelle note
                 * @var [type]
                 */
                $contatto->note = $params['device_id'];
                $contatto->contatto = $params['device_token'];
                $contatto->vendor = strtolower($params['device_vendor']);
                $contatto->type = UtlContatto::TYPE_DEVICE;
                if(!$contatto->save(false)){
                    ResponseError::returnSingleError( 422, 'Impossibile registrare informazioni device' );
                }

                if($to_link) {
                    $anagrafica->link('contatto',$contatto, ['use_type'=>UtlContatto::USE_TYPE_ALERT, 'type' => UtlContatto::TYPE_DEVICE]);
                }

                if($utente->tipo == 4) {
                    /**
                     * Operatore di sala, collego il token a quello
                     */
                    
                    $operatore = UtlOperatorePc::find()->where(['iduser'=>$utente->iduser])->one();

                    if($operatore) {
                        $c_r = $operatore->getContatto()
                        ->where(
                            [
                                'note'=>$params['device_id'],
                                'vendor'=>$contatto->vendor
                            ])
                        ->one();
                        if(!$c_r) {
                            $operatore->link('contatto', $contatto, ['use_type'=>UtlContatto::USE_TYPE_ALERT, 'type' => UtlContatto::TYPE_DEVICE]);
                        } else {
                            $c_r->contatto = $params['device_token'];//$contatto->contatto;
                            if(!$c_r->save()) {
                                ResponseError::returnMultipleErrors(422, $c_r->getErrors());
                            }
                            Yii::error('Il token già era collegato', 'api');
                        }
                    } else {
                        /**
                         * Non ho il record in mas rubrica, è probabilmente un operatore di sala
                         */
                        Yii::error('Non trovato operatore pc collegato', 'api');
                    }


                } else {
                    $mas_rubrica = MasRubrica::find()->where(['id_anagrafica'=>$anagrafica->id])->one();

                    if($mas_rubrica) {
                        $c_r = $mas_rubrica->getContatto()
                        ->where(
                            [
                                'note'=>$params['device_id'],
                                'vendor'=>$contatto->vendor
                            ])
                        ->one();
                        if(!$c_r) {
                            $mas_rubrica->link('contatto', $contatto, ['use_type'=>UtlContatto::USE_TYPE_ALERT, 'type' => UtlContatto::TYPE_DEVICE]);
                        } else {
                            $c_r->contatto = $params['device_token'];//$contatto->contatto;
                            if(!$c_r->save()) {
                                ResponseError::returnMultipleErrors(422, $c_r->getErrors());
                            }
                            Yii::error('Il token già era collegato', 'api');
                        }
                    } else {
                        /**
                         * Non ho il record in mas rubrica, è probabilmente un operatore di sala
                         */                        
                        Yii::error('Non trovato mas rubrica collegato', 'api');
                    }
                }
                
                
                    

                $utente->device_vendor = $params['device_vendor'];
                $utente->device_token = $params['device_token'];
                if(!$utente->save(false)){
                    ResponseError::returnSingleError( 422, 'Impossibile registrare informazioni device' );
                }

                
                
            } else {
                Yii::error('Non aggiorno token perchè mancanti dati importanti', 'api');
                Yii::error(json_encode(Yii::$app->request->post()), 'api');
            }

            return [
                'token' => "" . $token,
                'user' => User::findOne(Yii::$app->user->identity->id),
                'enabled' =>  (bool) $utente->enabled
            ];

        else :
            ResponseError::returnMultipleErrors( 422, $model->getErrors() );
        endif;
    }

    /**
     * Crea nuovo utente app
     * @return [type] [description]
     */
    public function actionCreate(){

        $postData = Yii::$app->request->post();

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        
        try {
            if(empty($postData['codfiscale'])) ResponseError::returnSingleError(422, "Codice fiscale obbligatorio");

            $codfiscale = strtoupper($postData['codfiscale']);
            $just_enabled = UtlUtente::find()
                                ->joinWith('anagrafica', true, 'RIGHT JOIN')
                                ->joinWith('user', true, 'RIGHT JOIN')
                                ->where(['utl_anagrafica.codfiscale' => $codfiscale])
                                ->count();

            

            if($just_enabled > 0) ResponseError::returnSingleError( 422, "Utente già abilitato sulla piattaforma, verificare la mail di conferma registrazione o procedere con il recupera password");


            if(empty($postData['email']) || empty($postData['tipo_organizzazione'])) ResponseError::returnSingleError( 422, "Compila tutti i campi");

            $tipo_utl_utente = $postData['tipo_organizzazione'] == 1 ? 3 : 2;
            $_user = false;
            $is_volontario = false;

            if( $postData['tipo_organizzazione'] == 1 ) {
                // cerco un volontario
                $volontario = VolVolontario::find()
                ->joinWith('anagrafica', true, "RIGHT JOIN")
                ->where(['utl_anagrafica.codfiscale' => $codfiscale])
                ->andWhere(['vol_volontario.operativo'=>true])
                ->one();
                // se non trovo un volontario potrebbe essere un operatore di sala operativa
                if(!$volontario) {

                    $tipo_utl_utente = 4;
                    /**
                     * L'operatore di sala inserisce la matricola invece del codice fiscale
                     */
                    $operatore = UtlOperatorePc::find()
                    ->joinWith('anagrafica', true, "RIGHT JOIN")
                    ->where(['utl_anagrafica.matricola' => $codfiscale])
                    ->one();

                    if(!$operatore) ResponseError::returnSingleError( 422, "Utente non riconosciuto nella piattaforma di Protezione Civile");

                    $_user = $operatore->getUser()->one();
                    if($postData['email'] != $_user->email) ResponseError::returnSingleError( 422, "Indirizzo email utente non valido");

                    $id_anagrafica = $operatore->id_anagrafica;

                } else {

                    $id_anagrafica = $volontario->id_anagrafica;
                    
                    $contatti = $volontario->getContatto()
                    ->andWhere(['or',
                       ['type'=>0],
                       ['type'=>1]
                    ])->andWhere(['contatto'=>$postData['email']])
                    ->one();

                    if(!$contatti) {
                        ResponseError::returnSingleError( 422, "L'indirizzo email inserito non corrisponde con quello registrato nell'anagrafe volontari. Utilizzare un altro indirizzo o aggiornare l'anagrafe");
                    } else {
                        $is_volontario = true;

                        $mas_rubrica = MasRubrica::find()->where(['id_anagrafica'=>$id_anagrafica])->one();
                        if(!$mas_rubrica) {
                            $mas_rubrica = new MasRubrica;
                            $mas_rubrica->ruolo = 'volontario';
                            $mas_rubrica->id_anagrafica = $id_anagrafica;
                            if(!$mas_rubrica->save()){
                                ResponseError::returnMultipleErrors( 422, $mas_rubrica->getErrors() );
                            }
                        }
                    }
                }   

                

            } else {

                if(empty($postData['codice_attivazione'])) ResponseError::returnSingleError(422, "Inserisci codice attivazione");
                // ente pubblico, verifico la mail
                $utente = UtlUtente::find()
                ->joinWith('anagrafica', true, 'RIGHT JOIN')
                ->where(['utl_anagrafica.codfiscale' => $codfiscale])
                ->one();

                if(!$utente) ResponseError::returnSingleError(404, "Utente non trovato");

                $anagrafica = UtlAnagrafica::findOne($utente->id_anagrafica);
                
                if(!$anagrafica || $postData['email'] != $anagrafica->email) {
                    ResponseError::returnSingleError( 422, "L'indirizzo email inserito non corrisponde con quello registrato nell'anagrafe volontari. Utilizzare un altro indirizzo o aggiornare l'anagrafe");
                }

                if($utente->codice_attivazione != $postData['codice_attivazione']) ResponseError::returnSingleError(422, "Codice attivazione errato");
                
            }

            // SALVO USER
            // SignUp Validation
            // se utente era già preso perchè operatore di sala operativa non ho bisogno di crearne uno nuovo
            if(!$_user) {
                
                $signup = new AppSignup();
                $postData['codfiscale'] = $codfiscale;
                $signup->load(['AppSignup' => $postData]);

                Yii::info(['user SIGNUP']);
                Yii::info($signup, 'api');


                $user = $signup->signup();
                if (!$user) {
                    //throw new Exception(json_encode($signup->getErrors()), 422);
                    ResponseError::returnMultipleErrors( 422, $signup->getErrors());
                }

                Yii::info('User -- Creato user OK: '.$user->username.'-'.$user->email, 'api');
                Yii::info(['user'=>$user]);
            
            } else {
                $user = $_user;
            }

                

            // SALVO UTENTE
            // Se tipo organizzazione == 1 creo un nuovo utente altrimenti prendo quello esistente
            $utente = $postData['tipo_organizzazione'] == 1 ? new UtlUtente() : $utente;
            $utente->scenario = 'createUtenteApp';
            $utente->iduser = $user->id;
            $utente->telefono = $postData['telefono'];
            $utente->tipo = $tipo_utl_utente;
            if($is_volontario) $utente->enabled = 1;

            if($tipo_utl_utente != 2){
                $utente->id_anagrafica = $id_anagrafica;
            }

            if(!$utente->save()){
                ResponseError::returnMultipleErrors( 422, $utente->getErrors());
            }

            //Send mail to user
            if(!empty($user)){

                $mail =  Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'registrazioneUtenteApi-html', 'text' => 'registrazioneUtenteApi-text'],
                        ['user' => $user]
                    )
                    ->setFrom([ Yii::$app->params['supportEmail'] => Yii::$app->params['APP_NAME'] ])
                    ->setTo($user->email)
                    ->setSubject('Registrazione utente - ' . Yii::$app->name)
                    ->send();

                if($mail){
                    Yii::info('Utente -- Invio mail OK: '.$user->email, 'api');
                }
            }

            $dbTrans->commit();

        } catch (Exception $e) {
            $dbTrans->rollBack();

            Yii::error('Errore registrazione utente KO', 'api');

            Yii::error(array(
                'status' => 422,
                'message' => $e->getMessage()
            ), 'api');

            throw $e;

        }

        return $user;

    }

    /**
     * Conferma registrazione
     * @return [type] [description]
     */
    public function actionConfirm(){

        Yii::$app->response->format = 'html';
        $token = Yii::$app->request->get('token');

        if (empty($token) || !is_string($token)) {
            $error = 'Registrazione fallita, contattare il responsabile del sistema';
        }

        $user = User::findOne(['auth_key' => $token]);
        if (!empty($user)) {
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);
            $error = null;
        }else{
            $error = 'Registrazione fallita, contattare il responsabile del sistema';
        }

        return $this->renderPartial('confirmRegistration', ["error" => $error]);
    }

    /**
     * Recupera password
     * @return [type] [description]
     */
    public function actionRecovery() {
        $user = User::find()->where(['email'=>Yii::$app->request->post('email')])->one();
        if(!$user) ResponseError::returnSingleError( 404, "Email errata");

        $utente = UtlUtente::findOne(['iduser'=>$user->id]);
        if(!$utente) ResponseError::returnSingleError( 422, "Non sei abilitato all'uso dell'app");

        $user->generatePasswordResetToken();
        $user->save();

        $url = Yii::$app->urlManagerApi->createUrl('auth/reset?token=' . $user->password_reset_token);
        
        $sent = SendMail::send(
            Yii::$app->params['adminEmail'], 
            $user->email,
            'Reset password',
            [],
            [
                'use_layout'=>true,
                'theme' => [
                    'html'=>'passwordResetTokenApi-html',
                    'text'=>'passwordResetTokenApi-text'
                ],
                'theme_vars' => [
                    'url'=>$url,
                    'username' => $user->username
                ]
            ]
        );  

        if(!$sent) ResponseError::returnSingleError( 500, "Errore invio mail");
        
        return ['message'=>'ok'];

    }

    /**
     * Reimposta la password
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function actionReset( ) {
        Yii::$app->response->format = 'html';
        $token = Yii::$app->request->get('token');
        
        try {
            $model = new ResetPasswordForm($token);
        } catch (\yii\base\InvalidParamException $e) {
            throw new \yii\web\BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Nuova password salvata.');

            return $this->renderPartial('reset', [
                'model' => $model,
                'set' => true
            ]);
        }

        return $this->renderPartial('reset', [
            'model' => $model,
            'set' => false
        ]);
    }

    /**
     * Profilo utente loggato
     * @return json
     */
    public function actionProfile(){
        
        $user = User::findOne(Yii::$app->user->identity->id);
        if(!$user) ResponseError::returnSingleError( 404, "Utente non trovato");
        
        $utl_utente = UtlUtente::find()->where(['iduser'=>Yii::$app->user->identity->id])->one();
        if(!$utl_utente) ResponseError::returnSingleError( 404, "Utente app non trovato");
        
        return [
            'user' => User::findOne(Yii::$app->user->identity->id),
            'enabled' => (bool) $utl_utente->enabled,
        ];
    }
}