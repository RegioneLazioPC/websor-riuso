<?php

namespace backend\controllers;

use Yii;
use common\models\VolOrganizzazione;
use common\models\VolConvenzione;
use common\models\VolOrganizzazioneSearch;
use common\models\VolSede;
use common\models\VolSedeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use common\models\LocComune;
use Exception;
use GuzzleHttp\Client;
use yii\data\ActiveDataProvider;

/**
 * OrganizzazioneVolontariatoController implements the CRUD actions for VolOrganizzazione model.
 */
class OrganizzazioneVolontariatoController extends Controller
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
                        Yii::error("Tentativo di accesso non autorizzato organizzazioni user: " . Yii::$app->user->getId());
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'add-convenzione', 'delete-convenzione'],
                        'permissions' => ['viewOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-sede'],
                        'permissions' => ['createSede']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-contatto-rubrica', 'delete-contatto-rubrica'],
                        'permissions' => ['manageRecapitiOrgs']
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all VolOrganizzazione models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VolOrganizzazioneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VolOrganizzazione model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new VolSedeSearch();

        
        $params = (isset(Yii::$app->request->queryParams['VolSedeSearch'])) ?
            ['VolSedeSearch' => array_merge(Yii::$app->request->queryParams['VolSedeSearch'], ['id_organizzazione' => $id])] :
            ['VolSedeSearch' => ['id_organizzazione' => $id]];
        $dataProvider = $searchModel->search($params);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new VolOrganizzazione model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VolOrganizzazione();


        if (Yii::$app->request->method == 'POST') :
            $datas = Yii::$app->request->post();

            try {
                // formatto le date per postgresql
                $data_albo_regionale = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_albo_regionale']);
                $datas['VolOrganizzazione']['data_albo_regionale'] = $data_albo_regionale->format('Y-m-d H:i:s');

                $data_scadenza_assicurazione = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_scadenza_assicurazione']);
                $datas['VolOrganizzazione']['data_scadenza_assicurazione'] = $data_scadenza_assicurazione->format('Y-m-d H:i:s');

                $model->load($datas);
                if (!$model->validate()) {
                    throw new \Exception("Errore salvataggio", 1);
                }


                if ($datas['VolOrganizzazione']['manual_zona_update'] && $datas['VolOrganizzazione']['manual_zona_update'] == 1) {
                    $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = \common\models\ZonaAllertaStrategy::getZonaManuale();
                    $datas['VolOrganizzazione']['zone_allerta'] = implode(",", $datas['VolOrganizzazione']['zone_allerta_array']);
                } else {
                    $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = $model->tipoOrganizzazione->update_zona_allerta_strategy;
                }

                $model->load($datas);
                if (!$model->save()) {
                    throw new \Exception("Errore salvataggio", 1);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        endif;

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Crea una nuova sede per organizzazione di volontariato
     * @return mixed
     */
    public function actionCreateSede($id)
    {
        $model = new VolSede();

        if (Yii::$app->request->method == 'POST') :
            try {
                $datas = Yii::$app->request->post();
                $datas['VolSede']['id_organizzazione'] = $id;
                if (empty($datas['VolSede']['lat']) || empty($datas['VolSede']['lon'])) :
                    $model->load($datas);
                    if (!$model->validate()) {
                        throw new Exception("Dati non validi");
                    }

                    $comune = LocComune::findOne($datas['VolSede']['comune']);
                    $address = $datas['VolSede']['indirizzo'] . " " .
                        $comune->comune . " (" .
                        $comune->provincia->sigla . ")";

                    $lat_lng = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . Yii::$app->params['google_key']);
                    $res = json_decode($lat_lng, true);

                    if (isset($res['results']) &&
                        isset($res['results'][0]) &&
                        isset($res['results'][0]['geometry']) &&
                        isset($res['results'][0]['geometry']['location']) &&
                        isset($res['results'][0]['geometry']['location']['lng'])
                    ) :
                        $datas['VolSede']['lat'] = $res['results'][0]['geometry']['location']['lat'];
                        $datas['VolSede']['lon'] = $res['results'][0]['geometry']['location']['lng'];
                    else :
                        throw new Exception("Errore nella ricerca delle coordinate");
                    endif;
                endif;



                $model->load($datas);
                if (!$model->save()) {
                    throw new \Exception($model->getErrors(), 1);
                }

                return $this->redirect(['view', 'id' => $id]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create-sede', [
                    'model' => $model
                ]);
            }
        endif;

        return $this->render('create-sede', [
            'model' => $model,
            'id' => $id
        ]);
    }

    /**
     * Crea una nuova convenzione per organizzazione di volontariato
     * @return mixed
     */
    public function actionAddConvenzione($id)
    {

        $model = $this->findModel($id);

        // Initi Guzzle Http Client
        $client = new Client();

        // Login
        try {
            $responseLogin = $client->request('POST', Yii::$app->params['mgo_api_base_url'] . 'v1/auth/auth/login', [
                'json' => ['username' => Yii::$app->params['mgo_api_username'], 'password' => Yii::$app->params['mgo_api_password']]
            ]);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::error("Errore login MGO");
        }


        if ($responseLogin && $responseLogin->getReasonPhrase() == 'OK') {
            // Get response and parse token
            $bodyResponse = $responseLogin->getBody()->getContents();
            $parsedBody = json_decode($bodyResponse, true);
            $token = $parsedBody['data']['token'];

            // Add convenzione
            try {
                $convezioneResponse = $client->request('POST', Yii::$app->params['mgo_api_base_url'] . 'interoperabilita/organizzazione/convenzione', [
                    'json' => ['num_elenco_territoriale' => $model->ref_id],
                    'query' => ['codistat' => Yii::$app->FilteredActions->comune->codistat],
                    'headers' => [
                        'Authorization' => "Bearer {$token}"
                    ]
                ]);

                // Get response
                $bodyConvenzione = $convezioneResponse->getBody()->getContents();
                $parsedBodyConvenzione = json_decode($bodyConvenzione, true);

                $convezione = new VolConvenzione();
                $convezione->id_organizzazione = $model->id;
                $convezione->id_ref = !empty($parsedBodyConvenzione['data']['id_organizzazione']) ? $parsedBodyConvenzione['data']['id_organizzazione'] : null;
                $convezione->num_riferimento = !empty($parsedBodyConvenzione['data']['num_riferimento']) ? (string)$parsedBodyConvenzione['data']['num_riferimento'] : null;

                if (!$convezione->save()) {
                    throw new \yii\web\HttpException(404, "Errore salvataggio convenzione MGO");
                }

                Yii::$app->session->setFlash('success', 'Convenzione creata correttamente');
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                Yii::error("Errore save convenzione MGO");
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Rimuove una convenzione per organizzazione di volontariato
     * @return mixed
     */
    public function actionDeleteConvenzione($id)
    {

        $model = $this->findModel($id);

        if (!empty($model->convenzione)) {
            // Initi Guzzle Http Client
            $client = new Client();

            // Login
            try {
                $responseLogin = $client->request('POST', Yii::$app->params['mgo_api_base_url'] . 'v1/auth/auth/login', [
                    'json' => ['username' => Yii::$app->params['mgo_api_username'], 'password' => Yii::$app->params['mgo_api_password']]
                ]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                Yii::error("Errore login MGO");
            }

            if ($responseLogin && $responseLogin->getReasonPhrase() == 'OK') {
                // Get response and parse token
                $bodyResponse = $responseLogin->getBody()->getContents();
                $parsedBody = json_decode($bodyResponse, true);
                $token = $parsedBody['data']['token'];

                // Add convenzione
                try {
                    $convezioneResponse = $client->request("DELETE", Yii::$app->params['mgo_api_base_url'] . 'interoperabilita/organizzazione/convenzione/' . Yii::$app->FilteredActions->comune->codistat . '/' . $model->ref_id, [
                        'headers' => [
                            'Authorization' => "Bearer {$token}"
                        ]
                    ]);

                    // Get response
                    $bodyConvenzione = $convezioneResponse->getBody()->getContents();
                    $parsedBodyConvenzione = json_decode($bodyConvenzione, true);

                    VolConvenzione::findOne($model->convenzione->id)->delete();

                    Yii::$app->session->setFlash('success', 'Convenzione cancellata correttamente');
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    Yii::error("Errore cancellazione convenzione MGO");
                }
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Updates an existing VolOrganizzazione model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->method == 'POST') :
            $datas = Yii::$app->request->post();

            if (!empty($model->id_sync)) {
                $model->scenario = VolOrganizzazione::SCENARIO_UPDATE_SYNCED;
            } else {
                // formatto le date per postgresql
                $data_albo_regionale = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_albo_regionale']);

                $datas['VolOrganizzazione']['data_albo_regionale'] = $data_albo_regionale->format('Y-m-d H:i:s');

                $data_scadenza_assicurazione = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_scadenza_assicurazione']);

                $datas['VolOrganizzazione']['data_scadenza_assicurazione'] = $data_scadenza_assicurazione->format('Y-m-d H:i:s');
            }

            if ($datas['VolOrganizzazione']['manual_zona_update'] && $datas['VolOrganizzazione']['manual_zona_update'] == 1) {
                $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = \common\models\ZonaAllertaStrategy::getZonaManuale();
                $datas['VolOrganizzazione']['zone_allerta'] = implode(",", $datas['VolOrganizzazione']['zone_allerta_array']);
            } else {
                $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = $model->tipoOrganizzazione->update_zona_allerta_strategy;
            }


            if ($model->load($datas) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        endif;


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing VolOrganizzazione model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the VolOrganizzazione model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VolOrganizzazione the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VolOrganizzazione::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Aggiunge un contatto ad un elemento di mas_rubrica
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionAddContattoRubrica($id)
    {

        $base_model = $this->findModel($id);

        $contatto = new \common\models\utility\UtlContatto;
        $contatto->load(Yii::$app->request->post());
        if ($contatto->save()) :
            $base_model->link('contatto', $contatto, ['use_type' => $contatto->use_type, 'type' => $contatto->type]);
        endif;

        $base_model->syncEverbridge();

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Elimina un contatto di un elemento di rubrica
     * @param  [type] $id   [description]
     * @return [type]                   [description]
     */
    public function actionDeleteContattoRubrica($id)
    {


        $c = \common\models\organizzazione\ConOrganizzazioneContatto::find()->where(['id' => $id])->one();
        if ($c) {
            $id_odv = $c->id_organizzazione;
            $c->delete();

            $base_model = $this->findModel($id_odv);
            $base_model->syncEverbridge();
        } else {
            throw new \yii\web\HttpException(404, "Recapito non trovato");
        }

        return $this->redirect(['view', 'id' => $base_model->id]);
    }
}
