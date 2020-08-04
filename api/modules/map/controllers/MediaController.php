<?php
namespace api\modules\map\controllers;
use Yii;

use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\InvalidRouteException;
use common\models\UplMedia;
use common\models\UplTipoMedia;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\helpers\Url;

/**
 * Media Controller API
 *
 */
class MediaController extends Controller
{
    
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    /**
     * Vedi file
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionViewMedia( $id )
    {

        $file = UplMedia::find()->where(['id'=>$id])->one();
        if(!$file) throw new NotFoundHttpException;

        $path = Yii::getAlias('@backend/uploads/');
        $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome;
        
        return Yii::$app->response->sendFile(
                $file_path, 
                $file->nome, ['inline'=>true]);
        
    }   

}