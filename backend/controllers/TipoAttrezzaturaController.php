<?php

namespace backend\controllers;

use Yii;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlAttrezzaturaTipoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\UtlAggregatoreTipologie;
/**
 * TipoAttrezzaturaController implements the CRUD actions for UtlAttrezzaturaTipo model.
 */
class TipoAttrezzaturaController extends Controller
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
                    if(Yii::$app->user){
                        Yii::error("Tentativo di accesso non autorizzato tipo attrezzatura user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['viewTipoAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createTipoAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'unlink'],
                        'permissions' => ['updateTipoAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteTipoAttrezzatura']
                    ]
                ]        
            ]
        ];
    }

    /**
     * Lists all UtlAttrezzaturaTipo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlAttrezzaturaTipoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UtlAttrezzaturaTipo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if( Yii::$app->request->method == 'POST' ) :
            
            $attrezzatura = $this->findModel($id);
            $params = Yii::$app->request->post('UtlAttrezzaturaTipo') ? Yii::$app->request->post('UtlAttrezzaturaTipo') : [];
            
            if ( isset($params['aggregatore']) && is_array($params['aggregatore'])) : 
                foreach ($params['aggregatore'] as $aggrega) :
                    $a = UtlAggregatoreTipologie::findOne($aggrega);
                    if ($a) : 
                        $a->link('tipiAttrezzatura', $attrezzatura);
                        $a->save();
                    endif;
                endforeach;            
            endif;
        endif;

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Rimuovi un aggregatore di tipologie
     * get id && aggregatore
     * @return [type] [description]
     */
    public function actionUnlink()
    {
        if(!Yii::$app->request->get('id') || !Yii::$app->request->get('aggregatore')) :
            return $this->redirect(['index']);
        endif;

        $model = $this->findModel(Yii::$app->request->get('id'));
        $aggregatore = (Yii::$app->request->get('aggregatore')) ? UtlAggregatoreTipologie::findOne(Yii::$app->request->get('aggregatore')) : false;

        if($aggregatore) :
            $aggregatore->unlink('tipiAttrezzatura', $model, true);
        endif;

        return $this->redirect(['view', 'id'=>$model->id]);
    }

    /**
     * Creates a new UtlAttrezzaturaTipo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlAttrezzaturaTipo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UtlAttrezzaturaTipo model.
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
     * Deletes an existing UtlAttrezzaturaTipo model.
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
     * Finds the UtlAttrezzaturaTipo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlAttrezzaturaTipo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlAttrezzaturaTipo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
