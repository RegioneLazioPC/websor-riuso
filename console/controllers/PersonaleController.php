<?php
namespace console\controllers;

use common\models\User;
use common\models\UtlOperatorePc;
use Exception;
use Yii;
use yii\console\Controller;

class PersonaleController extends Controller
{
    /**
     * Crea un operatore di sala per gli user che non lo hanno
     *
     * ./yii personale/create-user
     * @return [type] [description]
     */
    public function actionCreateUser()
    {
        $operatori = UtlOperatorePc::find()->where(['iduser'=>null])->all();
        foreach ($operatori as $operatore){

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {

                //Create user
                $user = new User();
                $user->username = $operatore->matricola;
                $user->email = 'websor.'.$operatore->matricola.'@protezionecivile.regione.it';
                $user->setPassword($operatore->matricola.substr($operatore->cognome, 0, 3));
                $user->generateAuthKey();
                $user->save(false);

                // Add Permission
                $auth = Yii::$app->authManager;
                switch($operatore->ruolo){
                    case "operatore":
                        $authorRole = $auth->getRole('operatore');
                        break;
                    case "funzionario SOP":
                        $authorRole = $auth->getRole('funzionarioSOP');
                        break;
                    case "funzionario SOR":
                        $authorRole = $auth->getRole('funzionarioSOR');
                        break;
                }

                $auth->assign($authorRole, $user->getId());

                // Save iduser in operatore
                $operatore->iduser = $user->getId();
                $operatore->username = $operatore->matricola;
                $operatore->password = $operatore->matricola.substr($operatore->cognome, 0, 3);

                $operatore->save(false);

                echo "create user".$operatore->iduser."\n";
                //echo print_r($operatore->nome,true)."\n";
                //error_log('ok');

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
                exit;
            }
            

        }
        echo "fatto\n";

    }
}