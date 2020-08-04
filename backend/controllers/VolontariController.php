<?php

namespace backend\controllers;

use Yii;
use common\models\VolVolontario;
use common\models\VolVolontarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\web\Response;
use common\models\VolSede;
use common\models\VolOrganizzazione;

use common\models\UtlAnagrafica;

/**
 * VolontariController implements the CRUD actions for VolVolontario model.
 */
class VolontariController extends Controller
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
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['viewVolontario']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createVolontario']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateVolontario']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteVolontario']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['populate-org-sedi'],
                        'permissions' => ['@']
                    ],                    
                ]
            ],
        ];
    }

    /**
     * Lists all VolVolontario models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VolVolontarioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VolVolontario model.
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
     * Creates a new VolVolontario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VolVolontario();
        
        if(Yii::$app->request->method == 'POST') {

            $conn = Yii::$app->db;
            $db = $conn->beginTransaction();

            try{

                $exist = UtlAnagrafica::find()->where(['codfiscale'=>Yii::$app->request->post('UtlAnagrafica')['codfiscale']])->one();
                if($exist) {
                    $anagrafica = $exist;
                } else {
                    $anagrafica = new UtlAnagrafica();
                    $anagrafica->load(Yii::$app->request->post());

                    if(!$anagrafica->validate()) throw new \Exception(json_encode($anagrafica->getErrors()), 1);

                    if($anagrafica->save()) throw new \Exception("Errore salvataggio", 1);
                }
                
                
                $model->load(Yii::$app->request->post());
                $model->id_anagrafica = $anagrafica->id;
                if(!$model->validate()) {
                    throw new \Exception(json_encode($model->getErrors()), 1);
                }
            
                if($model->save()) {
                    $model->link('anagrafica', $anagrafica);

                    $db->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                

            } catch(\Exception $e){

                $db->rollBack();
                Yii::$app->session->setFlash('error',$e->getMessage());

            }
            

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing VolVolontario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $anagrafica = $model->anagrafica;

        $anagrafica->load(Yii::$app->request->post());
        $anagrafica->save();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing VolVolontario model.
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
     * Finds the VolVolontario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VolVolontario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VolVolontario::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Popola la select
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionPopulateOrgSedi($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sedi = VolSede::find()->andWhere(['id_organizzazione' => $id])->all();
        $data = [['id'=>'','text'=>'']];
        foreach ($sedi as $sede) {
            $data[] = ['id' => $sede->id, 'text' => $sede->indirizzo . " - " . $sede->tipo];
        }
        return ['data' => $data];
    }
}
