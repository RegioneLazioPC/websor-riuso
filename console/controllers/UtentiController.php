<?php
namespace console\controllers;

use common\models\UtlUtente;
use Exception;
use Yii;
use yii\console\Controller;
use common\models\User;

class UtentiController extends Controller
{
    public $username;
    public $password;
    public $role;

    public function options($actionID)
    {
        return ['username','password','role'];
    }
    
    public function optionAliases()
    {
        return [
            'u' => 'username', 'p'=>'password', 'r' => 'role'
        ];
    }

    /**
     * Modifica password utente
     *
     * ./yii utenti/change-password
     * @return void
     */
    public function actionChangePassword()
    {
        $auth = Yii::$app->authManager;

        $user = User::find()->where(['username'=>$this->username])->one();

        if(!$user) :
            echo "Utente non trovato\n";
            return;
        endif;

        $user->setPassword($this->password);
        $user->generateAuthKey();

        if(!$user->save()) :
            print_r($user->getErrors());
        endif;

        echo "Aggiornato\n";
    }

    /**
     * Modifica ruolo utente
     *
     * ./yii utenti/change-role -r="Admin" -u="username"
     * @return void
     */
    public function actionChangeRole()
    {
        $auth = Yii::$app->authManager;

        $user = User::find()->where(['username'=>$this->username])->one();

        if(!$user) :
            echo "Utente non trovato\n";
            return;
        endif;

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($this->role);
        if($role) $auth->assign($role, $user->id);

        echo "Aggiornato\n";
    }

}