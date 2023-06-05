<?php

namespace backend\controllers;

use common\models\VolSchieramento;
use common\models\VolSchieramentoSearch;

use common\models\UtlAutomezzoSearch;
use common\models\UtlAttrezzaturaSearch;

use common\models\ConMezzoSchieramento;
use common\models\ConAttrezzaturaSchieramento;


use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SchieramentoController implements the CRUD actions for VolSchieramento model.
 */
class SchieramentoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
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
                        'actions' => ['index', 'view'],
                        'verbs' => ['GET'],
                        'permissions' => ['listSchieramento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'add-mezzo-list', 'add-mezzo', 'add-attrezzatura-list', 'add-attrezzatura',
                            'remove-attrezzatura', 'remove-mezzo'
                        ],
                        'verbs' => ['POST','GET'],
                        'permissions' => ['updateSchieramento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createSchieramento']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteSchieramento']
                    ],
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new VolSchieramentoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $search_mezzo = new ConMezzoSchieramento();
        $search_mezzo->scenario = 'search';
        $mezzo_data_provider = ($search_mezzo)->search(Yii::$app->request->queryParams);
        $mezzo_data_provider->query->andWhere(['id_vol_schieramento'=>$model->id]);

        $search_attrezzatura = new ConAttrezzaturaSchieramento();
        $search_attrezzatura->scenario = 'search';
        $attrezzatura_data_provider = ($search_attrezzatura)->search(Yii::$app->request->queryParams);
        $attrezzatura_data_provider->query->andWhere(['id_vol_schieramento'=>$model->id]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'mezzo_data_provider' => $mezzo_data_provider,
            'search_mezzo' => $search_mezzo,
            'attrezzatura_data_provider' => $attrezzatura_data_provider,
            'search_attrezzatura' => $search_attrezzatura
        ]);
    }

    
    public function actionCreate()
    {
        $model = new VolSchieramento();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionAddMezzoList($id)
    {
        $model = $this->findModel($id);

        $subquery = ConMezzoSchieramento::find()->select('id_utl_automezzo')->where(['id_vol_schieramento'=>$model->id]);

        $searchModel = new UtlAutomezzoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere([
            'not in',
            'utl_automezzo.id',
            $subquery
        ]);

        return $this->renderAjax('add-mezzo-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    public function actionAddMezzo($id)
    {
        $model = $this->findModel($id);

        $subquery = ConMezzoSchieramento::find()->select('id_utl_automezzo')->where(['id_vol_schieramento'=>$model->id]);

        $searchModel = new UtlAutomezzoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere([
            'not in',
            'utl_automezzo.id',
            $subquery
        ]);

        
        $tr = Yii::$app->db->beginTransaction();
        try {
            $post_data = Yii::$app->request->post('ConMezzoSchieramento');

            if (empty($post_data['id_utl_automezzo'])) {
                throw new \Exception("Seleziona il mezzo", 1);
            }
            if (!empty($post_data['date_from']) && empty($post_data['date_to'])) {
                throw new \Exception("Date non valide, o entrambe o nessuna", 1);
            }
            if (!empty($post_data['date_to']) && empty($post_data['date_from'])) {
                throw new \Exception("Date non valide, o entrambe o nessuna", 1);
            }
            
            $exist = ConMezzoSchieramento::find()->where(['id_vol_schieramento'=>$model->id, 'id_utl_automezzo'=>$post_data['id_utl_automezzo']])->one();
            if ($exist) {
                throw new \Exception("Mezzo già inserito", 1);
            }


            
            
            $conn = new ConMezzoSchieramento;
            $conn->id_vol_schieramento = $id;
            $conn->id_utl_automezzo = $post_data['id_utl_automezzo'];
            if (!empty($post_data['date_from'])) {
                $conn->date_from = $post_data['date_from'] . " 00:00:01";
            }
            if (!empty($post_data['date_to'])) {
                $conn->date_to = $post_data['date_to'] . " 23:59:59";
            }
            if (!$conn->save()) {
                throw new \Exception(json_encode($conn->getErrors()), 1);
            }
            
            $tr->commit();
           

            return $this->renderAjax('add-mezzo-list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
                'reload_pjax_main'=>true,
                'close_modal_add'=>true
            ]);
        } catch (\Exception $e) {
            $tr->rollBack();
            
            return $this->renderAjax('add-mezzo-list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function actionRemoveMezzo($id, $id_mezzo)
    {
        $model = $this->findModel($id);

        $tr = Yii::$app->db->beginTransaction();
        try {
            $exist = ConMezzoSchieramento::find()->where(['id_vol_schieramento'=>$model->id, 'id_utl_automezzo'=>$id_mezzo])->one();
            if (!$exist) {
                throw new \Exception("Mezzo non inserito", 1);
            }
            
            $exist->delete();

            $tr->commit();
            return $this->redirect([
                'view',
                'id' => $id,
                'tab' => 'mezzi'
            ]);
        } catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }
    }



    public function actionAddAttrezzaturaList($id)
    {
        $model = $this->findModel($id);

        $subquery = ConAttrezzaturaSchieramento::find()->select('id_utl_attrezzatura')->where(['id_vol_schieramento'=>$model->id]);

        $searchModel = new UtlAttrezzaturaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere([
            'not in',
            'utl_attrezzatura.id',
            $subquery
        ]);

        return $this->renderAjax('add-attrezzatura-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    public function actionAddAttrezzatura($id)
    {
        $model = $this->findModel($id);

        // for ajax cannot use redirect
        $subquery = ConAttrezzaturaSchieramento::find()->select('id_utl_attrezzatura')->where(['id_vol_schieramento'=>$model->id]);

        $searchModel = new UtlAttrezzaturaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere([
            'not in',
            'utl_attrezzatura.id',
            $subquery
        ]);

        $tr = Yii::$app->db->beginTransaction();
        try {
            $post_data = Yii::$app->request->post('ConAttrezzaturaSchieramento');

            if (empty($post_data['id_utl_attrezzatura'])) {
                throw new \Exception("Seleziona attrezzatura", 1);
            }
            if (!empty($post_data['date_from']) && empty($post_data['date_to'])) {
                throw new \Exception("Date non valide, o entrambe o nessuna", 1);
            }
            if (!empty($post_data['date_to']) && empty($post_data['date_from'])) {
                throw new \Exception("Date non valide, o entrambe o nessuna", 1);
            }

            $exist = ConAttrezzaturaSchieramento::find()->where(['id_vol_schieramento'=>$model->id, 'id_utl_attrezzatura'=>$post_data['id_utl_attrezzatura']])->one();
            if ($exist) {
                throw new \Exception("Attrezzatura già inserita", 1);
            }
            
            $conn = new ConAttrezzaturaSchieramento;
            $conn->id_vol_schieramento = $id;
            $conn->id_utl_attrezzatura = $post_data['id_utl_attrezzatura'];
            if (!empty($post_data['date_from'])) {
                $conn->date_from = $post_data['date_from'] . " 00:00:01";
            }
            if (!empty($post_data['date_to'])) {
                $conn->date_to = $post_data['date_to'] . " 23:59:59";
            }
            if (!$conn->save()) {
                throw new \Exception(json_encode($conn->getErrors()), 1);
            }
            
            $tr->commit();
            
            
            return $this->renderAjax('add-attrezzatura-list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
                'reload_pjax_main'=>true,
                'close_modal_add'=>true
            ]);
        } catch (\Exception $e) {
            $tr->rollBack();
            return $this->renderAjax('add-attrezzatura-list', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function actionRemoveAttrezzatura($id, $id_attrezzatura)
    {
        $model = $this->findModel($id);

        $tr = Yii::$app->db->beginTransaction();
        try {
            $exist = ConAttrezzaturaSchieramento::find()->where(['id_vol_schieramento'=>$model->id, 'id_utl_attrezzatura'=>$id_attrezzatura])->one();
            if (!$exist) {
                throw new \Exception("Attrezzatura non inserita", 1);
            }
            
            $exist->delete();

            $tr->commit();
            return $this->redirect([
                'view',
                'id' => $id,
                'tab' => 'attrezzature'
            ]);
        } catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }
    }

    protected function findModel($id)
    {
        if (($model = VolSchieramento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
