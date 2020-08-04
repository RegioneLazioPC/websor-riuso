<?php
/**
 * Created by PhpStorm.
 * User: montes
 * Date: 02/05/2017
 * Time: 12:11
 */

namespace backend\models;

use common\models\ConOperatoreEvento;
use common\models\UtlOperatorePc;
use Yii;

class UserEvents {

    public static function handleBeforeLogout($event)
    {
        error_log('Logout');

        // Delete tutti gli eventi assegnati all'operatore
        $operatore = UtlOperatorePc::find()->where(['iduser' => Yii::$app->user->identity->getId()])->one();
        $models = [];
        if($operatore) $models = ConOperatoreEvento::find()->where(['idoperatore' => $operatore->id])->all();

        if(count($models) > 0){
            foreach ($models as $model) {
                $model->delete();
            }
        }
    }
}