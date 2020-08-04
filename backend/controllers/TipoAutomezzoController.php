<?php

namespace backend\controllers;

use Yii;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAutomezzoTipoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\UtlAggregatoreTipologie;
/**
 * TipoAutomezzoController implements the CRUD actions for UtlAutomezzoTipo model.
 */
class TipoAutomezzoController extends Controller
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
                        Yii::error("Tentativo di accesso non autorizzato tipo automezzo user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['viewTipoAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createTipoAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'unlink'],
                        'permissions' => ['updateTipoAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteTipoAutomezzo']
                    ]
                ]        
            ]
        ];
    }

    /**
     * Lists all UtlAutomezzoTipo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlAutomezzoTipoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UtlAutomezzoTipo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        if( Yii::$app->request->method == 'POST' ) :
            
            $automezzo = $this->findModel($id);
            $params = Yii::$app->request->post('UtlAutomezzoTipo') ? Yii::$app->request->post('UtlAutomezzoTipo') : [];
            
            if ( isset($params['aggregatore']) && is_array($params['aggregatore'])) : 
                foreach ($params['aggregatore'] as $aggrega) :
                    $a = UtlAggregatoreTipologie::findOne($aggrega);
                    if ($a) : 
                        $a->link('tipiAutomezzo', $automezzo);
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
            $aggregatore->unlink('tipiAutomezzo', $model, true);
        endif;

        return $this->redirect(['view', 'id'=>$model->id]);
    }

    /**
     * Creates a new UtlAutomezzoTipo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlAutomezzoTipo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UtlAutomezzoTipo model.
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
     * Deletes an existing UtlAutomezzoTipo model.
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
     * Finds the UtlAutomezzoTipo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlAutomezzoTipo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlAutomezzoTipo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
