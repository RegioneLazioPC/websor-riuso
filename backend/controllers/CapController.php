<?php

namespace backend\controllers;

use Yii;
use common\models\cap\CapResources;
use common\models\cap\CapConsumer;
use common\models\cap\CapConsumerSearch;
use common\models\cap\ViewCapMessagesGrouped;
use common\models\cap\CapResourcesSearch;
use common\models\cap\CapMessages;
use common\utils\cap\CapFeed;
use common\models\cap\ViewCapVehiclesReportCurrent;
use common\models\cap\ViewCapVehiclesReport;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

use yii\web\Response;

/**
 * CapController implements the CRUD actions for RubricaGroup model.
 */
class CapController extends Controller
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
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['risorse'],
                        'verbs' => ['GET','POST'],
                        'permissions' => ['listCapResources']
                    ],[
                        'allow' => true,
                        'actions' => ['vehicles'],
                        'verbs' => ['GET'],
                        'permissions' => ['viewListCapVehicles']
                    ],[
                        'allow' => true,
                        'actions' => ['vehicles-history'],
                        'verbs' => ['GET'],
                        'permissions' => ['viewListCapVehiclesHistory']
                    ],[
                        'allow' => true,
                        'actions' => ['list-message'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['listCapMessages']
                    ],[
                        'allow' => true,
                        'actions' => ['single-message'],
                        'verbs' => ['GET'],
                        'permissions' => ['viewCapMessage']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-risorsa'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['createCapResource']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['ripristina-risorsa'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['createCapResource']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-risorsa'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['updateCapResource']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['lock-unlock-risorsa'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['updateCapResource']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-risorsa'],
                        'verbs' => ['GET'],
                        'permissions' => ['deleteCapResource']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['rilascia-semaforo'],
                        'verbs' => ['GET'],
                        'permissions' => ['updateCapResource']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['parse'],
                        'verbs' => ['GET'],
                        'permissions' => ['@']
                    ],


                    [
                        'allow' => true,
                        'actions' => ['consumers'],
                        'verbs' => ['GET'],
                        'permissions' => ['viewConsumer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-consumer'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['createCapConsumer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-consumer'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['updateCapConsumer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-consumer-password'],
                        'verbs' => ['GET', 'POST'],
                        'permissions' => ['updateCapConsumerPassword']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-consumer'],
                        'verbs' => ['GET'],
                        'permissions' => ['deleteCapConsumer']
                    ],
                ]
            ],
        ];
    }

    public function actionParse()
    {
        $seconds = 20;
        $parsed = [];
        if (isset(Yii::$app->params['cap_parsing_seconds'])) {
            $seconds = Yii::$app->params['cap_parsing_seconds'];
        }

        $feed = CapResources::find()
        ->where(['removed'=>0, 'locked'=>0])
        ->andWhere('(last_check < :t OR last_check is null)', [':t'=> time()-$seconds])
        ->orderBy(['id'=>SORT_ASC])
        ->all();


        foreach ($feed as $risorsa) {
            $token = sem_get($risorsa->getSemaphore(), 1);

            $acquired = sem_acquire($token, true);
            if (!$acquired) {
                continue;
            }

            try {
                $last_messages = CapMessages::find()
                    ->where(['id_resource'=>$risorsa->id])
                    ->orderBy(['sent'=>SORT_DESC])
                    ->limit(100)
                    ->all();
                $to_exclude = array_map(function ($message) {
                    return $message->url;
                }, $last_messages);

                $f = new CapFeed($risorsa);
                $f->excludeUrls($to_exclude);
                $f->loadItems();

                $n = 0;
                $f->parseItems(function ($i) use (&$n, $risorsa) {
                    // aumento il time limit di 30 secondi
                    set_time_limit(30);
                    CapMessages::buildFromResource($i, $risorsa);

                    $n++;
                });

                $risorsa->last_check = time();
                if (!$risorsa->save()) {
                    throw new \Exception(json_encode($risorsa->getErrors()), 1);
                }

                $parsed[] = $risorsa->identifier;
                sem_release($token);
            } catch (\Exception $e) {
                sem_release($token);
                Yii::error($e, 'cap');
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['parsati' => $parsed];
    }

    
    public function actionRisorse()
    {
        $searchModel = new CapResourcesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('risorse', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    public function actionListMessage()
    {
        $searchModel = new ViewCapMessagesGrouped();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list-message', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    public function actionSingleMessage($id)
    {
        $model = CapMessages::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('single-message', [
            'model' => $model
        ]);
    }

    /**
     * Crea nuova risorsa CAP
     * @return mixed
     */
    public function actionCreateRisorsa()
    {
        $model = new CapResources();

        // sto aggiungendo o togliendo contatti
        if (Yii::$app->request->method == 'POST') {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $model->load(Yii::$app->request->post());
                $model->password = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->password, Yii::$app->params['cap_password_secret_key']));
                if (!$model->save()) {
                    throw new \Exception("Errore inserimento nuova risorsa", 1);
                }

                $trans->commit();

                return $this->redirect(['risorse']);
            } catch (\Exception $e) {
                $trans->rollBack();
                Yii::error("Errore inserimento risorsa: " . $e->getMessage());
            }
        }

        $model->password = '';

        return $this->render('create-risorsa', [
            'model' => $model
        ]);
    }

    /**
     * Aggiorna risorsa CAP
     * @return mixed
     */
    public function actionUpdateRisorsa($id)
    {
        $model = CapResources::findOne($id);
        if (!$model) {
            throw new \yii\web\HttpException(404, "Risorsa non trovata");
        }


        // sto aggiungendo o togliendo contatti
        if (Yii::$app->request->method == 'POST') {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $model->load(Yii::$app->request->post());
                $posted = Yii::$app->request->post('CapResources');
                if (isset($posted['password']) && !empty($posted['password'])) {
                    $model->password = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->password, Yii::$app->params['cap_password_secret_key']));
                }

                if (!$model->save()) {
                    throw new \Exception("Errore inserimento nuova risorsa", 1);
                }

                $trans->commit();

                return $this->redirect(['risorse']);
            } catch (\Exception $e) {
                $trans->rollBack();
                Yii::error("Errore inserimento risorsa: " . $e->getMessage());
            }
        }

        $model->password = '';

        return $this->render('update-risorsa', [
            'model' => $model
        ]);
    }

    /**
     * Elimina risorsa CAP
     * @return mixed
     */
    public function actionDeleteRisorsa($id)
    {
        $model = CapResources::findOne($id);
        if (!$model) {
            throw new \yii\web\HttpException(404, "Risorsa non trovata");
        }

        $model->locked = 1;
        $model->removed = 1;
        $model->save();

        return $this->redirect(['risorse']);
    }

    /**
     * Ripristina risorsa CAP
     * @return mixed
     */
    public function actionRipristinaRisorsa($id)
    {
        $model = CapResources::findOne($id);
        if (!$model) {
            throw new \yii\web\HttpException(404, "Risorsa non trovata");
        }

        $model->removed = 0;
        $model->save();

        return $this->redirect(['risorse']);
    }

    /**
     * Riattiva risorsa CAP
     * @return mixed
     */
    public function actionLockUnlockRisorsa($id)
    {
        $model = CapResources::findOne($id);
        if (!$model) {
            throw new \yii\web\HttpException(404, "Risorsa non trovata");
        }

        $model->locked = $model->locked == 1 ? 0 : 1;
        $model->save();

        return $this->redirect(['risorse']);
    }

    /**
     * Rilascia semaforo risorsa CAP
     * @return mixed
     */
    public function actionRilasciaSemaforo($id)
    {
        $model = CapResources::findOne($id);
        if (!$model) {
            throw new \yii\web\HttpException(404, "Risorsa non trovata");
        }

        $semaphore = $model->getSemaphore();
        Yii::error('ID SEMAFORO: ' . $semaphore);
        $token = sem_get($semaphore, 1);
        sem_remove($token);

        return $this->redirect(['risorse']);
    }

    /**
     * Lista dei consumer
     * @return mixed
     */
    public function actionConsumers()
    {
        $searchModel = new CapConsumerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('consumers', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create consumer
     * @return mixed
     */
    public function actionCreateConsumer()
    {
        $model = new CapConsumer();
        $model->scenario = CapConsumer::SCENARIO_CREATE;

        // sto aggiungendo o togliendo contatti
        if (Yii::$app->request->method == 'POST') {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $post_data = Yii::$app->request->post('CapConsumer');

                $model->load(Yii::$app->request->post());

                $model->comuni = (!empty($post_data['comuni'])) ? json_encode($post_data['comuni']) : null;

                $model->generateAuthKey();
                $model->impostaPassword(Yii::$app->request->post('CapConsumer')['password']);

                if (!$model->save()) {
                    $trans->rollBack();
                    return $this->render('create-consumer', [
                        'model' => $model
                    ]);
                }

                $trans->commit();

                return $this->redirect(['consumers']);
            } catch (\Exception $e) {
                echo $e->getMessage();
                die();
                $trans->rollBack();
            }
        }


        return $this->render('create-consumer', [
            'model' => $model
        ]);
    }

    /**
     * Aggiorna dati consumer
     * @return mixed
     */
    public function actionUpdateConsumer($id)
    {
        $model = CapConsumer::findOne($id);
        $model->scenario = CapConsumer::SCENARIO_UPDATE;

        // sto aggiungendo o togliendo contatti
        if (Yii::$app->request->method == 'POST') {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $post_data = Yii::$app->request->post('CapConsumer');

                $model->load(Yii::$app->request->post());

                $model->comuni = (!empty($post_data['comuni'])) ? json_encode($post_data['comuni']) : null;

                if (!$model->save()) {
                    $trans->rollBack();
                    return $this->render('update-consumer', [
                        'model' => $model
                    ]);
                }

                $trans->commit();

                return $this->redirect(['consumers']);
            } catch (\Exception $e) {
                die($e->getMessage());
                $trans->rollBack();
            }
        }


        return $this->render('update-consumer', [
            'model' => $model
        ]);
    }

    /**
     * Aggiorna password consumer
     * @return mixed
     */
    public function actionUpdateConsumerPassword($id)
    {
        $model = CapConsumer::findOne($id);
        $model->scenario = CapConsumer::SCENARIO_CHANGE_PASSWORD;

        // sto aggiungendo o togliendo contatti
        if (Yii::$app->request->method == 'POST') {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $model->generateAuthKey();
                $model->impostaPassword(Yii::$app->request->post('CapConsumer')['password']);

                if (!$model->save()) {
                    $trans->rollBack();
                    return $this->render('update-consumer-password', [
                        'model' => $model
                    ]);
                }

                $trans->commit();

                return $this->redirect(['consumers']);
            } catch (\Exception $e) {
                $trans->rollBack();
            }
        }

        return $this->render('update-consumer-password', [
            'model' => $model
        ]);
    }

    /**
     * Elimina consumer
     * @return mixed
     */
    public function actionDeleteConsumer($id)
    {
        $model = CapConsumer::findOne($id);
        $model->delete();

        return $this->redirect(['consumers']);
    }

    public function actionVehicles()
    {
        $searchModel = new ViewCapVehiclesReportCurrent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list-vehicles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVehiclesHistory()
    {
        $searchModel = new ViewCapVehiclesReport();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list-vehicles-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
