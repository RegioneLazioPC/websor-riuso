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

use api\utils\ResponseError;


use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\LocProvincia;
use common\models\LocComune;
use common\models\TblSezioneSpecialistica;
use common\models\UtlTipologia;
use common\models\UtlRuoloSegnalatore;
use common\models\UtlSegnalazione;
use common\models\UtlAggregatoreTipologie;

/**
 * Stub Controller
 *
 */
class StubController extends ActiveController
{
    public $modelClass = 'common\models\UtlEvento';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] =[
                'class' => \api\utils\Authenticator::class,
                'except' => ['options', 'layers', 'data','geojson']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options', 'layers','data','geojson'],
            /*'rules' => [
                [
                    'allow' => true,
                    'actions' => [],
                    'roles' => ['@']
                ]
            ]*/
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
     * Ritorna lista di layer configurati in common/config/params-local.php
     * @return [type] [description]
     */
    public function actionLayers (  ) 
    {

        return Yii::$app->params['geoserver_layers'];
        
    }

    /**
     * Lista di dati stub necessari al client
     * @return [type] [description]
     */
    public function actionData() {
        
        $key = "stub_data_cartografico";
        $data = Yii::$app->cache->get($key);

        if(!$data) {

            $mappatura_categorie_aggregatori = UtlAggregatoreTipologie::find()->with(['categoria'])->all();
            $temp_agg = [];
            foreach ($mappatura_categorie_aggregatori as $aggregatore) {
                $temp_agg[$aggregatore->id] = $aggregatore->categoria->id;
            }

            $mappatura_categorie_aggregatori = $temp_agg;

            $data = [
                'tipo_automezzi' => UtlAutomezzoTipo::find()->with(['aggregatori','aggregatori.categoria'])->asArray()->all(),
                'tipo_attrezzature' => UtlAttrezzaturaTipo::find()->with(['aggregatori','aggregatori.categoria'])->asArray()->all(),
                'aggregatori' => UtlAggregatoreTipologie::find()->all(),
                'categorie' => UtlCategoriaAutomezzoAttrezzatura::find()->all(),
                'province' => LocProvincia::find()->where(['id_regione'=>Yii::$app->params['region_filter_id']])->all(),
                'comuni' => LocComune::find()->joinWith(['provincia'])->asArray()->where(['loc_provincia.id_regione'=>Yii::$app->params['region_filter_id']])->orderBy('comune')->all(),
                'specializzazioni'=>TblSezioneSpecialistica::find()->all(),
                'tipi_evento' => UtlTipologia::find()->all(),
                'ruoli_segnalatore' => UtlRuoloSegnalatore::find()->all(),
                'fonti_segnalazione' => UtlSegnalazione::getFonteArray(),
                'mappatura_categorie_aggregatori' => $mappatura_categorie_aggregatori
            ];

            Yii::$app->cache->set($key, $data, 60*60);
        }


        return $data;
    }


}
