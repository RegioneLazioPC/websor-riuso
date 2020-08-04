<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\AppSignup;
use common\models\LoginForm;
use common\models\MyHelper;
use common\models\User;
use common\models\UtlUtente;
use Exception;
use linslin\yii2\curl\Curl;
use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use common\models\UtlAnagrafica;

/**
 * Utenti Controller API
 * @deprecated 
 */
class UtentiController extends ActiveController
{
    public $modelClass = 'common\models\UtlUtente';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update']);
        return $actions;
    }

   
    public function actionUpdate($id)
    {
        $params['UtlUtente']=Yii::$app->request->getBodyParams();
        $model = UtlUtente::find()->where(['utl_utente.id'=>$id])->joinWith('user')->one();
        if (!$model) :
            return array(
                'status' => 500,
                'message' => 'Errore salvataggio dari'
            );
        endif;
        $user = User::findOne($model->iduser);

        //Change registritation flow: Update username and password
        if($model->telefono != $user->username){
            $user->username = $model->telefono;
            $user->setPassword($model->smscode);
            $user->save();
        }

        $model->scenario = 'createUtenteApp';

        if ($model->load($params) && $model->save()) {

            return array(
                'status' => 200,
                'response' => $model
            );

        } else {

            return array(
                'status' => 500,
                'message' => 'Errore salvataggio dari'
            );
        }

    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = 'json';

        $model = new LoginForm();
        $postData = array(
            'LoginForm' => Yii::$app->request->post()
        );

        if ($model->load($postData) && $model->login()) {
            $userId = Yii::$app->user->id;

            $utente = UtlUtente::find()->where(['iduser'=>$userId])->with(['user'])->asArray()->one();

            Yii::info('Utente - Login OK'.$utente['nome'].'-'.$utente['cognome'], 'api');

            return array(
                'status' => 200,
                'response' => $utente
            );
        } else {
            Yii::error('Utente - Login KO: username o password non corretti', 'api');
            return array(
                'status' => 500,
                'message' => 'Errore, username o password non corretti!'
            );
        }
    }

    /**
     * Send SMS code
     *
     * @return mixed
     */
    public function actionCheckSmsCode()
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = 'json';

        $idUtente = Yii::$app->request->post('idUtente');
        $smsCode = Yii::$app->request->post('smsCode');

        // Check Utente
        $utente = UtlUtente::find()->where(['utl_utente.id' => $idUtente, 'smscode' => $smsCode])->joinWith('user')->asArray()->one();

        // Activate User
        if(!empty($utente)){
            $user = User::findOne($utente['iduser']);
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);
        }

        if (!empty($utente) && !empty($user)) {

            return array(
                'status' => 200,
                'response' => $utente
            );
        } else {
            return array(
                'status' => 500,
                'message' => 'Errore, sms code o utente non corretti!'
            );
        }
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        if (empty($token) || !is_string($token)) {
            Yii::error('Utente -- Reset password KO, Token non può essere vuoto', 'api');
            return array(
                'status' => 500,
                'message' => 'Token non può essere vuoto!'
            );
        }

        $password = Yii::$app->request->post('newpassword');

        if(!empty($password) && strlen($password) >= 6){

            $user = User::findByPasswordResetToken($token);

            if($user){
                $user->setPassword($password);
                $user->removePasswordResetToken();
                $user->save(false);

                Yii::info('Utente -- Reset password - password salvata correttamente OK: '.$user->username.'-'.$user->email, 'api');

                return array(
                    'status' => 200,
                    'message' => 'Password salvata correttamente! Procedere con la login'
                );

            }else{

                Yii::error('Utente -- Reset password KO, Utente non trovato verificare il token inviato', 'api');

                return array(
                    'status' => 500,
                    'message' => 'Utente non trovato, verificare il token inviato.'
                );
            }

        }else{

            Yii::error('Utente -- Reset password KO, La password non può essere vuota e deve contenere almeno 6 caratteri', 'api');

            return array(
                'status' => 500,
                'message' => 'La password non può essere vuota e deve contenere almeno 6 caratteri!'
            );
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionRequestNewPassword()
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = 'json';

        $userdata = Yii::$app->request->post('userdata');

        $user = User::find()
            ->where('status=:status', [':status' => User::STATUS_ACTIVE])
            ->andWhere(['or', 'email=:userdata', 'username=:userdata'])
            ->addParams([':userdata' => $userdata])
            ->one();

        if(!$user){

            Yii::info('Utente -- Richiesta nuova password - KO : username o email non valide', 'api');

            return array(
                'status' => 500,
                'message' => 'Errore, username o email non valide!'
            );
        }

        Yii::info('Utente -- Richiesta nuova password: '.$user->username.'-'.$user->email, 'api');

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return array(
                    'status' => 500,
                    'message' => 'Errore, generazione token!'
                );
            }
        }


        if ($user) {

            $mail =  Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'passwordResetTokenApi-html', 'text' => 'passwordResetTokenApi-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' app-noreply'])
                ->setTo($user->email)
                ->setSubject('Password reset - ' . Yii::$app->name)
                ->send();

            if($mail){

                Yii::info('Utente -- Generazione invio nuova password OK: '.$user->username.'-'.$user->email, 'api');

                return array(
                    'status' => 200,
                    'response' => 'Abbiamo inviato una mail per il recupero della password, torna alla login una volta effettuata la modifica'
                );
            }else{

                Yii::error('Utente -- Errore invio mail nuova password - KO', 'api');
                return array(
                    'status' => 500,
                    'message' => 'Errore, invio mail!'
                );
            }

        } else {
            Yii::error('Utente -- Richiesta nuova password, email inserita non è corretta - KO', 'api');
            return array(
                'status' => 500,
                'message' => 'Errore, la mail fornita non è corretta!'
            );
        }
    }

   
    /**
     * Check if utente is attivo
     *
     * @return mixed
     */
    public function actionIsUserActive($id)
    {
        // Controllo se esiste l'utente e se non è attivo
        $utente =  UtlUtente::find()->where(['utl_utente.id' => $id, 'user.status' => User::STATUS_DELETED])->joinWith('user')->one();
        
        if (empty($utente)) {

            return array(
                'status' => 200,
                'response' => true
            );
        } else {
            return array(
                'status' => 500,
                'message' => false
            );
        }
    }
}


