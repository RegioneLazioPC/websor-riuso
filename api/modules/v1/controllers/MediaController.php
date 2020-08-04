<?php
namespace api\modules\v1\controllers;
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

    /**
     * Ritorna un file da web
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionViewMediaFromWeb( $image )
    {
        $path = Yii::getAlias('@backend').'/web/images/'.$image;
        if(file_exists($path)) {
            return Yii::$app->response->sendFile(
                $path, 
                'img', ['inline'=>true]);
        } else {
            throw new NotFoundHttpException();
        }
        
    }   

    /**
     * Ritorna un marker
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionViewMediaFromWebMarkers( $image )
    {
        $path = Yii::getAlias('@backend').'/web/images/markers/'.$image;
        if(file_exists($path)) { 

            return Yii::$app->response->sendFile(
                $path, 
                'img', ['inline'=>true]);
        } else {
            throw new NotFoundHttpException();
            
        }
        
    }   

}