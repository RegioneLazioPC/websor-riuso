<?php

namespace backend\controllers;

use Yii;
use common\models\AlmAllertaMeteo;
use common\models\AlmAllertaMeteoSearch;


use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\MasMessage;

use common\models\ViewRubrica;
use common\models\ViewRubricaSearch;
use common\models\RubricaGroup;
use common\models\RubricaGroupSearch;

use common\models\MasInvio;
use common\models\MasSingleSend;

use common\models\LocComune;
use common\models\LocComuneSearch;

use common\models\ConMasInvioContact;
use common\models\ConMasInvioContactSearch;

use yii\db\Query;
use yii\db\Expression;

use common\models\AlmZonaAllerta;

use common\utils\MasMessageManager;
use common\models\MasMessageTemplate;
use yii\web\Response;
use yii\helpers\Url;

/**
 * AllertaMeteoController
 */
class AllertaMeteoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user) {
                        Yii::error("Tentativo di accesso non autorizzato automezzo user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['listAllerte']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'send-allerta', 'modal-preview', 'get-zone'],
                        'permissions' => ['createAllerta']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateAllerta']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteAllerta']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['zone-allerta'],
                        'permissions' => ['Admin']
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all AlmAllertaMeteo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlmAllertaMeteoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lista comuni con zone di allerta
     * @return [type] [description]
     */
    public function actionZoneAllerta()
    {
        $searchModel = new LocComuneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('zone-allerta', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AlmAllertaMeteo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AlmAllertaMeteo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AlmAllertaMeteo();

        if (Yii::$app->request->method == 'POST') {
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            try {
                $data = Yii::$app->request->post();
                
                $data['AlmAllertaMeteo']['data_allerta'] = \DateTime::createFromFormat(
                    'd-m-Y',
                    $data['AlmAllertaMeteo']['data_allerta']
                )->format('Y-m-d');
                if ($model->load($data) && $model->save()) {
                    $file = UploadedFile::getInstance($model, 'mediaFile');
                    
                    $tipo = \common\models\UplTipoMedia::find()->where(
                        ['descrizione'=>'Allerta meteo']
                    )->one();

                    $media = new \common\models\UplMedia;
                    $media->uploadFile($file, $tipo->id);
                    $media->refresh();

                    $model->id_media = $media->id;
                    $model->save();
                    
                    $dbTrans->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AlmAllertaMeteo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AlmAllertaMeteo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AlmAllertaMeteo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AlmAllertaMeteo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlmAllertaMeteo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



    public function actionSendAllerta()
    {
        
        $model = new AlmAllertaMeteo;
        $messaggio = new MasMessage;


        /**
         * crea un'allerta completa con tutti i destinatari
         * @var [type]
         */
        if (Yii::$app->request->method == 'POST') {
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();
            
            try {
                $data = Yii::$app->request->post();
                
                $data['AlmAllertaMeteo']['data_allerta'] = \DateTime::createFromFormat(
                    'd-m-Y',
                    $data['AlmAllertaMeteo']['data_allerta']
                )->format('Y-m-d');

                $zone = array_map(function ($z) {
                    return $z->code;
                }, AlmZonaAllerta::find()->orderBy(['code'=>SORT_ASC])->all());
                $zone_data = [];
                foreach ($zone as $codice_zona) {
                    if (isset($data['AlmAllertaMeteo']['zone_allerta_array'][$codice_zona]) &&
                        $data['AlmAllertaMeteo']['zone_allerta_array'][$codice_zona] == 1) {
                        $zone_data[] = $codice_zona;
                    }
                }

                $data['AlmAllertaMeteo']['zone_allerta'] = implode(",", $zone_data);
                
                $model->load($data);

                $files = UploadedFile::getInstances($model, 'mediaFile');
                $messaggio->load(Yii::$app->request->post());

                if (empty($files) || !$files) {
                    throw new \Exception("Inserisci un file", 1);
                }
                
                if ($model->save()) {
                    $tipo = \common\models\UplTipoMedia::find()->where(
                        ['descrizione'=>'Allerta meteo']
                    )->one();
                    

                    foreach ($files as $file) {
                        $media = new \common\models\UplMedia;
                        $media->uploadFile($file, $tipo->id, MasMessage::validAllertaMimes());
                        $media->refresh();

                        $model->link('file', $media);
                    }
                    
                    $messaggio->load(Yii::$app->request->post());
                    $messaggio->id_allerta = $model->id;
                    if ($messaggio->save()) {
                        $invio = new MasInvio;
                        $invio->id_message = $messaggio->id;
                        $invio->channel_mail = $messaggio->channel_mail;
                        $invio->channel_pec = $messaggio->channel_pec;
                        $invio->channel_push = $messaggio->channel_push;
                        $invio->channel_sms = $messaggio->channel_sms;
                        $invio->channel_fax = $messaggio->channel_fax;
                        $invio->data_invio = date("Y-m-d H:m:s", time());

                        if (!$invio->save()) {
                            throw new \Exception("Dati invio non validi");
                        }

                        if (isset(Yii::$app->params['mas_version']) && Yii::$app->params['mas_version'] == 2) {
                            $id_message_mas = \common\utils\MasV2Dispatcher::createMessage($messaggio, $invio);
                            $invio->mas_ref_id = (string) $id_message_mas;
                            if (!$invio->save()) {
                                throw new \Exception("Errore creazione messaggio", 1);
                            }
                        }
                        /**
                         * @todo  from here
                         */
                        $dbTrans->commit();
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'id_invio' => $invio->id,
                            'redirect_url' => Url::to(['mas/view-invio', 'id_invio' => $invio->id])
                        ];
                    }
                }
            } catch (\Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                
                if (Yii::$app->request->method == 'POST') {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['error'=>$e->getMessage()];
                } else {
                    return $this->render('send-allerta', [
                        'model' => $model,
                        'messaggio' => $messaggio,
                    ]);
                }
            }
        }

        return $this->render('send-allerta', [
            'model' => $model,
            'messaggio' => $messaggio
        ]);
    }

    /**
     * Mostra anteprima messaggio
     * @param  [type] $id_template [description]
     * @return [type]              [description]
     */
    public function actionModalPreview()
    {

        $id_template = Yii::$app->request->post('id_template');
        $channel = Yii::$app->request->post('channel');
        
        $template = null;
        if (!empty($id_template)) {
            $template = MasMessageTemplate::findOne($id_template);
        }

        $model = new MasMessage;
        
        $model->mail_text = Yii::$app->request->post('content');
        $model->fax_text = Yii::$app->request->post('content');
        $model->sms_text = Yii::$app->request->post('content');
        $model->push_text = Yii::$app->request->post('content');
        
        
        return MasMessageManager::getPreview($model, $template, $channel);
    }

    /**
     * Prendi zone di allerta
     * @return [type] [description]
     */
    public function actionGetZone()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $zone_geo_json = AlmZonaAllerta::find()->select(['code',
            'ST_AsGeoJSON( (ST_DUMP( ST_Transform( geom, 4326) )).geom::geometry(Polygon, 4326)) as geojson'
        ])->asArray()->all();

        $colors = [
            '#FFFF00',
            '#808000',
            '#00FF00',
            '#008000',
            '#00FFFF',
            '#008080',
            '#0000FF',
            '#000080',
            '#800080',
            '#C0C0C0'
        ];

        $zn_c = [];

        foreach ($zone_geo_json as $zona) {
            if (!isset($zn_c[$zona['code']])) {
                $zn_c[$zona['code']] = [];
            }

            $zn_c[$zona['code']][] = $zona['geojson'];
        }

        return $zn_c;
    }
}
