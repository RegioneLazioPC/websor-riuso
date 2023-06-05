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
                'except' => ['options', 'layers', 'data', 'geojson']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options', 'layers','data','geojson'],
            
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
    public function actionOptions()
    {
        return ['message'=>'ok'];
    }

   
    /**
     * Ritorna lista di layer configurati in common/config/params-local.php
     * @return [type] [description]
     */
    public function actionLayers()
    {
        
        return Yii::$app->params['geoserver_layers'];
    }

    /**
     * Lista di dati stub necessari al client
     * @return [type] [description]
     */
    public function actionData()
    {

        
        $key = "stub_data_cartografico";
        $data = Yii::$app->cache->get($key);

        if (!$data) {
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

    /**
     * Only for testing
     */
    public function actionGeojson()
    {
        return;
        return json_decode('
            {
                "type": "FeatureCollection",
                "features": [
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.8457377,
                                41.5046519
                            ]
                        },
                        "properties": {
                            "id": 3690651,
                            "device_id": 6,
                            "device_name": "RL05 CASSINO",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2021-06-23 11:44:20",
                            "local_timestamp": "2021-06-23 13:44:20",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.5046519,
                            "longitude": 13.8457377,
                            "speed": 0,
                            "course": 0,
                            "altitude": 55,
                            "locality": "Via Filieri, San Pasquale, San Silvestro, Cassino, Frosinone, Lazio, 03043, Italia",
                            "diff_seconds": 14208,
                            "diff_meters": 50,
                            "host_ip": "83.225.7.42:49153",
                            "opacity": 1
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                12.239279,
                                42.0673519
                            ]
                        },
                        "properties": {
                            "id": 3015914,
                            "device_id": 5,
                            "device_name": "RL 05 TEST",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:59:29",
                            "local_timestamp": "2021-06-16 18:09:00",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3403846,
                            "longitude": 13.311725,
                            "speed": 161,
                            "course": 314,
                            "altitude": 500,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 438,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 1
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.311725,
                                41.3403846
                            ]
                        },
                        "properties": {
                            "id": 3015913,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:59:29",
                            "local_timestamp": "2019-07-17 15:59:29",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3403846,
                            "longitude": 13.311725,
                            "speed": 161,
                            "course": 314,
                            "altitude": 138,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 438,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 1
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.313299,
                                41.3366309
                            ]
                        },
                        "properties": {
                            "id": 3015912,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:59:19",
                            "local_timestamp": "2019-07-17 15:59:19",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3366309,
                            "longitude": 13.313299,
                            "speed": 151,
                            "course": 310,
                            "altitude": 126,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 415,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.98
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3144697,
                                41.3330077
                            ]
                        },
                        "properties": {
                            "id": 3015911,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:59:09",
                            "local_timestamp": "2019-07-17 15:59:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3330077,
                            "longitude": 13.3144697,
                            "speed": 148,
                            "course": 307,
                            "altitude": 90,
                            "locality": "Via Appia, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 394,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.96
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3148494,
                                41.3294721
                            ]
                        },
                        "properties": {
                            "id": 3015910,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:58:59",
                            "local_timestamp": "2019-07-17 15:58:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3294721,
                            "longitude": 13.3148494,
                            "speed": 133,
                            "course": 299,
                            "altitude": 62,
                            "locality": "Via Fosso Sant\'Antonio, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 291,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.94
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3156563,
                                41.3269235
                            ]
                        },
                        "properties": {
                            "id": 3015909,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:58:49",
                            "local_timestamp": "2019-07-17 15:58:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3269235,
                            "longitude": 13.3156563,
                            "speed": 66,
                            "course": 315,
                            "altitude": 52,
                            "locality": "Via Fosso Sant\'Antonio, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 22,
                            "diff_meters": 139,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.92
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3173094,
                                41.3268181
                            ]
                        },
                        "properties": {
                            "id": 3015908,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:58:27",
                            "local_timestamp": "2019-07-17 15:58:27",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3268181,
                            "longitude": 13.3173094,
                            "speed": 1,
                            "course": 250,
                            "altitude": 13,
                            "locality": "Via Orione, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 28,
                            "diff_meters": 110,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.9
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3182995,
                                41.3274679
                            ]
                        },
                        "properties": {
                            "id": 3015907,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:57:59",
                            "local_timestamp": "2019-07-17 15:57:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3274679,
                            "longitude": 13.3182995,
                            "speed": 72,
                            "course": 175,
                            "altitude": 42,
                            "locality": "Via Orione, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 248,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.88
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3169124,
                                41.3294376
                            ]
                        },
                        "properties": {
                            "id": 3015906,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:57:49",
                            "local_timestamp": "2019-07-17 15:57:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3294376,
                            "longitude": 13.3169124,
                            "speed": 107,
                            "course": 148,
                            "altitude": 61,
                            "locality": "Via Orione, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 333,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.86
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3150021,
                                41.33207
                            ]
                        },
                        "properties": {
                            "id": 3015905,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:57:39",
                            "local_timestamp": "2019-07-17 15:57:39",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.33207,
                            "longitude": 13.3150021,
                            "speed": 127,
                            "course": 154,
                            "altitude": 91,
                            "locality": "Via Appia, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 357,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.84
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3134068,
                                41.3350532
                            ]
                        },
                        "properties": {
                            "id": 3015904,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:57:29",
                            "local_timestamp": "2019-07-17 15:57:29",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3350532,
                            "longitude": 13.3134068,
                            "speed": 134,
                            "course": 161,
                            "altitude": 118,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 376,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.82
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3120588,
                                41.3382807
                            ]
                        },
                        "properties": {
                            "id": 3015903,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:57:19",
                            "local_timestamp": "2019-07-17 15:57:19",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3382807,
                            "longitude": 13.3120588,
                            "speed": 132,
                            "course": 163,
                            "altitude": 135,
                            "locality": "Via Chivi, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 356,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.8
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3108067,
                                41.3413374
                            ]
                        },
                        "properties": {
                            "id": 3015902,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:57:09",
                            "local_timestamp": "2019-07-17 15:57:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3413374,
                            "longitude": 13.3108067,
                            "speed": 125,
                            "course": 165,
                            "altitude": 131,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 370,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.78
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.309648,
                                41.3445495
                            ]
                        },
                        "properties": {
                            "id": 3015901,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:56:59",
                            "local_timestamp": "2019-07-17 15:56:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3445495,
                            "longitude": 13.309648,
                            "speed": 137,
                            "course": 165,
                            "altitude": 142,
                            "locality": "Via Chivi, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 371,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.76
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3083965,
                                41.3477539
                            ]
                        },
                        "properties": {
                            "id": 3015900,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:56:49",
                            "local_timestamp": "2019-07-17 15:56:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3477539,
                            "longitude": 13.3083965,
                            "speed": 132,
                            "course": 161,
                            "altitude": 152,
                            "locality": "Via Galleria Mont\'Orso, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 389,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.74
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3067682,
                                41.3510358
                            ]
                        },
                        "properties": {
                            "id": 3015899,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:56:39",
                            "local_timestamp": "2019-07-17 15:56:39",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3510358,
                            "longitude": 13.3067682,
                            "speed": 147,
                            "course": 161,
                            "altitude": 158,
                            "locality": "Via Iris, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 433,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.72
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3052378,
                                41.3547598
                            ]
                        },
                        "properties": {
                            "id": 3015898,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:56:29",
                            "local_timestamp": "2019-07-17 15:56:29",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3547598,
                            "longitude": 13.3052378,
                            "speed": 160,
                            "course": 159,
                            "altitude": 166,
                            "locality": "Via delle Margherite, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 436,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.7
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3027575,
                                41.3582127
                            ]
                        },
                        "properties": {
                            "id": 3015897,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:56:19",
                            "local_timestamp": "2019-07-17 15:56:19",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3582127,
                            "longitude": 13.3027575,
                            "speed": 155,
                            "course": 148,
                            "altitude": 180,
                            "locality": "Via San Giuseppe, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 437,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.68
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.2998061,
                                41.3614539
                            ]
                        },
                        "properties": {
                            "id": 3015896,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:56:09",
                            "local_timestamp": "2019-07-17 15:56:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3614539,
                            "longitude": 13.2998061,
                            "speed": 149,
                            "course": 144,
                            "altitude": 193,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 273,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.66
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.2983641,
                                41.3636573
                            ]
                        },
                        "properties": {
                            "id": 3015895,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:55:59",
                            "local_timestamp": "2019-07-17 15:55:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3636573,
                            "longitude": 13.2983641,
                            "speed": 49,
                            "course": 265,
                            "altitude": 219,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 226,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.64
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3006854,
                                41.3626157
                            ]
                        },
                        "properties": {
                            "id": 3015894,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:55:49",
                            "local_timestamp": "2019-07-17 15:55:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3626157,
                            "longitude": 13.3006854,
                            "speed": 96,
                            "course": 306,
                            "altitude": 186,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 291,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.62
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3031035,
                                41.3607336
                            ]
                        },
                        "properties": {
                            "id": 3015893,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:55:39",
                            "local_timestamp": "2019-07-17 15:55:39",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3607336,
                            "longitude": 13.3031035,
                            "speed": 114,
                            "course": 327,
                            "altitude": 184,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 344,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.6
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.30498,
                                41.3579751
                            ]
                        },
                        "properties": {
                            "id": 3015892,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:55:29",
                            "local_timestamp": "2019-07-17 15:55:29",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3579751,
                            "longitude": 13.30498,
                            "speed": 131,
                            "course": 319,
                            "altitude": 178,
                            "locality": "Via delle Margherite, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 384,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.58
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3069061,
                                41.354835
                            ]
                        },
                        "properties": {
                            "id": 3015891,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:55:19",
                            "local_timestamp": "2019-07-17 15:55:19",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.354835,
                            "longitude": 13.3069061,
                            "speed": 145,
                            "course": 321,
                            "altitude": 186,
                            "locality": "Via del Ginepro, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 415,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.56
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3091094,
                                41.3514869
                            ]
                        },
                        "properties": {
                            "id": 3015890,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:55:09",
                            "local_timestamp": "2019-07-17 15:55:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3514869,
                            "longitude": 13.3091094,
                            "speed": 150,
                            "course": 320,
                            "altitude": 190,
                            "locality": "Via Iris, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 415,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.54
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3103273,
                                41.3478724
                            ]
                        },
                        "properties": {
                            "id": 3015889,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:54:59",
                            "local_timestamp": "2019-07-17 15:54:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3478724,
                            "longitude": 13.3103273,
                            "speed": 152,
                            "course": 300,
                            "altitude": 175,
                            "locality": "Via Galleria Mont\'Orso, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 428,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.52
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3104613,
                                41.3440289
                            ]
                        },
                        "properties": {
                            "id": 3015888,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:54:49",
                            "local_timestamp": "2019-07-17 15:54:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3440289,
                            "longitude": 13.3104613,
                            "speed": 149,
                            "course": 298,
                            "altitude": 153,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 400,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.5
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3117639,
                                41.3405626
                            ]
                        },
                        "properties": {
                            "id": 3015887,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:54:39",
                            "local_timestamp": "2019-07-17 15:54:39",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3405626,
                            "longitude": 13.3117639,
                            "speed": 142,
                            "course": 318,
                            "altitude": 140,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 383,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.48
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3135335,
                                41.3373829
                            ]
                        },
                        "properties": {
                            "id": 3015886,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:54:29",
                            "local_timestamp": "2019-07-17 15:54:29",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3373829,
                            "longitude": 13.3135335,
                            "speed": 135,
                            "course": 307,
                            "altitude": 136,
                            "locality": "Via Chivi, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 354,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.46
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3143434,
                                41.3342569
                            ]
                        },
                        "properties": {
                            "id": 3015885,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:54:19",
                            "local_timestamp": "2019-07-17 15:54:19",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3342569,
                            "longitude": 13.3143434,
                            "speed": 125,
                            "course": 304,
                            "altitude": 125,
                            "locality": "Il Gusto della Valle, Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 329,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.44
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3148955,
                                41.3313299
                            ]
                        },
                        "properties": {
                            "id": 3015884,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:54:09",
                            "local_timestamp": "2019-07-17 15:54:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3313299,
                            "longitude": 13.3148955,
                            "speed": 113,
                            "course": 303,
                            "altitude": 97,
                            "locality": "Via Appia, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 309,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.42
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3151186,
                                41.328552
                            ]
                        },
                        "properties": {
                            "id": 3015883,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:53:59",
                            "local_timestamp": "2019-07-17 15:53:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.328552,
                            "longitude": 13.3151186,
                            "speed": 99,
                            "course": 297,
                            "altitude": 68,
                            "locality": "Via Fosso Sant\'Antonio, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 213,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.4
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3157898,
                                41.3267059
                            ]
                        },
                        "properties": {
                            "id": 3015882,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:53:49",
                            "local_timestamp": "2019-07-17 15:53:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3267059,
                            "longitude": 13.3157898,
                            "speed": 54,
                            "course": 302,
                            "altitude": 51,
                            "locality": "Via Fosso Sant\'Antonio, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 13,
                            "diff_meters": 116,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.38
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3171749,
                                41.3267573
                            ]
                        },
                        "properties": {
                            "id": 3015881,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:53:36",
                            "local_timestamp": "2019-07-17 15:53:36",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3267573,
                            "longitude": 13.3171749,
                            "speed": 13,
                            "course": 244,
                            "altitude": 19,
                            "locality": "Via Fosso Sant\'Antonio, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 27,
                            "diff_meters": 105,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.36
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3182742,
                                41.3272084
                            ]
                        },
                        "properties": {
                            "id": 3015880,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:53:09",
                            "local_timestamp": "2019-07-17 15:53:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3272084,
                            "longitude": 13.3182742,
                            "speed": 54,
                            "course": 196,
                            "altitude": 37,
                            "locality": "Via Orione, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 208,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.34
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.317306,
                                41.3289339
                            ]
                        },
                        "properties": {
                            "id": 3015879,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:52:59",
                            "local_timestamp": "2019-07-17 15:52:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3289339,
                            "longitude": 13.317306,
                            "speed": 100,
                            "course": 150,
                            "altitude": 57,
                            "locality": "Via Orione, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 303,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.32
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3157115,
                                41.3313773
                            ]
                        },
                        "properties": {
                            "id": 3015878,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:52:49",
                            "local_timestamp": "2019-07-17 15:52:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3313773,
                            "longitude": 13.3157115,
                            "speed": 119,
                            "course": 155,
                            "altitude": 80,
                            "locality": "Via Appia, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 333,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.3
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3141218,
                                41.3341241
                            ]
                        },
                        "properties": {
                            "id": 3015877,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:52:39",
                            "local_timestamp": "2019-07-17 15:52:39",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3341241,
                            "longitude": 13.3141218,
                            "speed": 119,
                            "course": 160,
                            "altitude": 99,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 340,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.28
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3128049,
                                41.3370151
                            ]
                        },
                        "properties": {
                            "id": 3015876,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:52:29",
                            "local_timestamp": "2019-07-17 15:52:29",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3370151,
                            "longitude": 13.3128049,
                            "speed": 123,
                            "course": 161,
                            "altitude": 112,
                            "locality": "Via Chivi, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 340,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.26
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3114442,
                                41.3399006
                            ]
                        },
                        "properties": {
                            "id": 3015875,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:52:19",
                            "local_timestamp": "2019-07-17 15:52:19",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3399006,
                            "longitude": 13.3114442,
                            "speed": 124,
                            "course": 159,
                            "altitude": 138,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 370,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.24
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3099788,
                                41.3430387
                            ]
                        },
                        "properties": {
                            "id": 3015874,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:52:09",
                            "local_timestamp": "2019-07-17 15:52:09",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3430387,
                            "longitude": 13.3099788,
                            "speed": 145,
                            "course": 162,
                            "altitude": 146,
                            "locality": "Via di Mezzo, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 419,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.22
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3082551,
                                41.3465804
                            ]
                        },
                        "properties": {
                            "id": 3015873,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:51:59",
                            "local_timestamp": "2019-07-17 15:51:59",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3465804,
                            "longitude": 13.3082551,
                            "speed": 149,
                            "course": 156,
                            "altitude": 156,
                            "locality": "Via Galleria Mont\'Orso, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 413,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.2
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.306402,
                                41.3500199
                            ]
                        },
                        "properties": {
                            "id": 3015872,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:51:49",
                            "local_timestamp": "2019-07-17 15:51:49",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3500199,
                            "longitude": 13.306402,
                            "speed": 147,
                            "course": 160,
                            "altitude": 174,
                            "locality": "Via Galleria Mont\'Orso, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 419,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.18
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.304833,
                                41.3536033
                            ]
                        },
                        "properties": {
                            "id": 3015871,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:51:39",
                            "local_timestamp": "2019-07-17 15:51:39",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3536033,
                            "longitude": 13.304833,
                            "speed": 154,
                            "course": 161,
                            "altitude": 185,
                            "locality": "Via delle Margherite, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 11,
                            "diff_meters": 467,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.16
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3025273,
                                41.3574279
                            ]
                        },
                        "properties": {
                            "id": 3015870,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:51:28",
                            "local_timestamp": "2019-07-17 15:51:28",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3574279,
                            "longitude": 13.3025273,
                            "speed": 150,
                            "course": 153,
                            "altitude": 185,
                            "locality": "Via dell\'Antenna, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 413,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.14
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3002165,
                                41.3607147
                            ]
                        },
                        "properties": {
                            "id": 3015869,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:51:18",
                            "local_timestamp": "2019-07-17 15:51:18",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3607147,
                            "longitude": 13.3002165,
                            "speed": 144,
                            "course": 149,
                            "altitude": 200,
                            "locality": "Via Grotte, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 333,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.12
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.2982512,
                                41.3633163
                            ]
                        },
                        "properties": {
                            "id": 3015868,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:51:08",
                            "local_timestamp": "2019-07-17 15:51:08",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3633163,
                            "longitude": 13.2982512,
                            "speed": 72,
                            "course": 181,
                            "altitude": 223,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 129,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.1
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.2997902,
                                41.3631901
                            ]
                        },
                        "properties": {
                            "id": 3015867,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:50:58",
                            "local_timestamp": "2019-07-17 15:50:58",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3631901,
                            "longitude": 13.2997902,
                            "speed": 78,
                            "course": 303,
                            "altitude": 186,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 245,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.08
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3019613,
                                41.3617059
                            ]
                        },
                        "properties": {
                            "id": 3015866,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:50:48",
                            "local_timestamp": "2019-07-17 15:50:48",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3617059,
                            "longitude": 13.3019613,
                            "speed": 104,
                            "course": 322,
                            "altitude": 181,
                            "locality": "Via Valle Imperiale, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 335,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.06
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3039817,
                                41.3590976
                            ]
                        },
                        "properties": {
                            "id": 3015865,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:50:38",
                            "local_timestamp": "2019-07-17 15:50:38",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3590976,
                            "longitude": 13.3039817,
                            "speed": 139,
                            "course": 321,
                            "altitude": 172,
                            "locality": "Via San Giuseppe, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 426,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.04
                        }
                    },
                    {
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                13.3062148,
                                41.3556524
                            ]
                        },
                        "properties": {
                            "id": 3015864,
                            "device_id": 4,
                            "device_name": "RL 04 GAETA",
                            "device_group_id": 1,
                            "device_group_name": "Regione Lazio",
                            "utc_timestamp": "2019-07-17 13:50:28",
                            "local_timestamp": "2019-07-17 15:50:28",
                            "event_code": "A",
                            "event_name": "Posizione",
                            "latitude": 41.3556524,
                            "longitude": 13.3062148,
                            "speed": 164,
                            "course": 323,
                            "altitude": 165,
                            "locality": "Via delle Margherite, Vallemarina, Monte San Biagio, LT, LAZ, Italia",
                            "diff_seconds": 10,
                            "diff_meters": 454,
                            "host_ip": "83.225.8.49:4097",
                            "opacity": 0.02
                        }
                    }
                ]
            }', true);
    }
}
