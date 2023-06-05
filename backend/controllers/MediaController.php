<?php
namespace backend\controllers;
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
 * @author Fabio Rizzo
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

        $behaviors =  [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view-media' => ['GET'],
                    'upload' => ['POST']
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [                    
                    'viewMedia','upload'
                ],
                'rules'=>[
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ]
        ];
        return $behaviors;
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'list',
    ];    

    
    public function actionMarker($name, $w, $h) {

        $f = false;
        $path =  Yii::getAlias('@backend/web/images/markers/'.$name);
        $resized = Yii::getAlias($path."w=".$w."h=".$h);
        if(file_exists($resized)) {
            header('Content-Type: image/png');
            header('Content-Length: ' . filesize($resized));
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type');
            header('Access-Control-Allow-Methods: GET');
            readfile($resized);
            exit(0);
        }

        if(file_exists($path))
        {
            $f = Yii::getAlias($path);
        } else{
            $path = Yii::getAlias('@backend/web/images/markers/evento-default.png');
            $f = Yii::getAlias($path);
            $resized = Yii::getAlias($path."w=".$w."h=".$h);
            if(file_exists($resized)) {
                header('Content-Type: image/png');
                header('Content-Length: ' . filesize($resized));
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Headers: Content-Type');
                header('Access-Control-Allow-Methods: GET');
                readfile($resized);
                exit(0);
            } 
        }

        
        $img = imagecreatefrompng($f);
        list($width, $height) = getimagesize($f);

        $tmp = imagecreatetruecolor($w, $h);
        imagealphablending($tmp, false);
        imagesavealpha($tmp,true);
        $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
        imagefilledrectangle($tmp, 0, 0, $w, $h, $transparent);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $w, $h, $width, $height);

        
        imagepng($tmp, "$resized");

        header('Content-Type: image/png');
        header('Content-Length: ' . filesize($resized));
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: GET');
        readfile($resized);
        exit;
    }


    /**
     * Ritorna un file per il cartografico
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionViewMediaCartografico( $id, $orientation = null )
    {
        
        $file = UplMedia::find()->where(['id'=>$id])->one();
        if(!$file) throw new NotFoundHttpException;

        $path = Yii::getAlias('@backend/uploads/');
        if( $orientation ) {
            
            $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome . '.oriented';
            if(!file_exists($file_path)) $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome;

        } else {

            $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome;
            
        }
        
        header('Content-Type: ' . $file->mime_type);
        header('Content-Length: ' . filesize($file_path));
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: GET');
        readfile($file_path);
        exit;
        
    } 

    /**
     * Ritorna un file
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionViewMedia( $id )
    {
        
        $file = UplMedia::find()->where(['id'=>$id])->one();
        if(!$file) {
            throw new NotFoundHttpException;
        }

        $path = Yii::getAlias('@backend/uploads/');
        if(Yii::$app->request->get('oriented')) {
            
            $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome . '.oriented';
            if(!file_exists($file_path)) $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome;

        } else {

            $file_path = $path.$file->ext.'/'.$file->date_upload.'/'.$file->nome;

        }
        
        if(!file_exists($file_path)) {
            throw new NotFoundHttpException;
        }

        return Yii::$app->response->sendFile(
                $file_path, 
                $file->nome, ['inline'=>true]);
    } 


    /**
     * Upload immagine
     */
    public function actionUpload() {
        
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file');                    
        $tipo = \common\models\UplTipoMedia::find()->where(
            ['descrizione'=>'Immagine editor']
        )->one();

        $media = new \common\models\UplMedia;
        $media->uploadFile($file, $tipo->id, [ 'image/jpeg','image/png','image/jpg'] );
        $media->refresh();

        
        return Yii::$app->urlManagerApi->getBaseUrl().'/media/view-media/'.$media->id;

    }

    

}