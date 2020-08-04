<?php

namespace api\modules\map\controllers;

use Exception;
use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use common\models\VolOrganizzazione;

use common\models\cartografia\ViewCartografiaAutomezzo;
use common\models\cartografia\ViewCartografiaAttrezzatura;
use api\utils\ResponseError;

use common\models\organizzazione\ViewOrganizzazioniAttive;
/**
 * Organizzazione Controller
 *
 */
class OrganizzazioneController extends ActiveController
{
    public $modelClass = 'common\models\VolOrganizzazione';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] =[
                'class' => \api\utils\Authenticator::class,
                'except' => ['options','all','view']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options','all','view'],
           
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Di default per il metodo options torniamo ok in modo da non avere errori not found dalle chiamate automatiche del browser
     * @return [type] [description]
     */
    public function actionOptions() {
        return ['message'=>'ok'];
    }

    /**
     * Vedi organizzazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionView($id) {
        $org = VolOrganizzazione::find()->where(['id'=>$id])->with(
            [
                'automezzi','automezzi.tipo','attrezzature','attrezzature.tipo','volSedes','volSedes.locComune'
            ])->asArray()->one();
        if(!$org) ResponseError::returnSingleError(404, "Organizzazione non trovata");
        
        return $org;
    }

    /**
     * Mostra tutte le organizzazioni
     * @return [type] [description]
     */
    public function actionAll( ) {
        return ViewOrganizzazioniAttive::find()->all();
    }
    

}
