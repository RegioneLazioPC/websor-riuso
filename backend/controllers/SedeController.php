<?php

namespace backend\controllers;

use Yii;
use common\models\VolSede;
use common\models\VolSedeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\UtlAutomezzoSearch;
use common\models\UtlAttrezzaturaSearch;
/**
 * SedeController implements the CRUD actions for VolSede model.
 */
class SedeController extends Controller
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
        ];
    }

    /**
     * Lists all VolSede models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VolSedeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VolSede model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $automezzoparams = ( isset(Yii::$app->request->queryParams['UtlAutomezzoSearch']) ) ? 
        ['UtlAutomezzoSearch'=> array_merge(Yii::$app->request->queryParams['UtlAutomezzoSearch'],['idsede'=>$id])] : 
        ['UtlAutomezzoSearch'=>['idsede'=>$id]];
        
        $automezzosearchModel = new UtlAutomezzoSearch();
        $automezzodataProvider = $automezzosearchModel->search(
            $automezzoparams
        );

        $attrezzaturaparams = ( isset(Yii::$app->request->queryParams['UtlAttrezzaturaSearch']) ) ? 
        ['UtlAttrezzaturaSearch'=> array_merge(Yii::$app->request->queryParams['UtlAttrezzaturaSearch'],['idsede'=>$id])] : 
        ['UtlAttrezzaturaSearch'=>['idsede'=>$id]];

        $attrezzaturasearchModel = new UtlAttrezzaturaSearch();
        $attrezzaturadataProvider = $attrezzaturasearchModel->search(
            $attrezzaturaparams
        );

        return $this->render('view', [
            'model' => $this->findModel($id),
            'automezzosearchModel' => $automezzosearchModel,
            'automezzodataProvider' => $automezzodataProvider,
            'attrezzaturasearchModel' => $attrezzaturasearchModel,
            'attrezzaturadataProvider' => $attrezzaturadataProvider,
        ]);
    }

    /**
     * Creates a new VolSede model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VolSede();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing VolSede model.
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
     * Deletes an existing VolSede model.
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
     * Finds the VolSede model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VolSede the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VolSede::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
