<?php
namespace console\controllers;


use Yii;
use yii\console\Controller;
use yii\helpers\BaseConsole as Console;
use yii\base\Exception;

use common\models\UplMedia;

class UploadController extends Controller
{    

    /**
     * Cancellazione immagini vecchie
     * 
     * Il created_at minimo dipende dai giorni inseriti in params-local.php (parametro image_old_days)
     * 
     * Comando: ./yii upload/remove-old
     * 
     * @return void
     */
    public function actionRemoveOld( $days ) {

        $max_time = time() - (60*60*24* (int) $days );

        $tipo_media = UplTipoMedia::find()->where(['descrizione'=>'Immagine segnalazione'])->one();
        if($tipo_media) {
            $media = UplMedia::find()->where(['<','created_at',$max_time])->andWhere(['id_tipo_media'=>$tipo->id])->all();
            foreach ($media as $m) {
                $m->delete();
            }
        }

        echo "IMMAGINI CANCELLATE\n";
        
    }

}