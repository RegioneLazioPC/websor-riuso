<?php

namespace backend\controllers;

use Yii;
use common\models\UtlAttrezzatura;
use common\models\UtlAttrezzaturaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\Response;
use common\models\VolSede;
use common\models\UtlAutomezzo;
/**
 * AttrezzaturaController implements the CRUD actions for UtlAttrezzatura model.
 */
class AttrezzaturaController extends Controller
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
                        Yii::error("Tentativo di accesso non autorizzato automezzo user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['viewAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteAttrezzatura']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['populate-org-sedi','populate-org-sede-automezzi'],
                        'permissions' => ['@']
                    ]
                ]        
            ]
        ];
    }

    /**
     * Lists all UtlAttrezzatura models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlAttrezzaturaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UtlAttrezzatura model.
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
     * Creates a new UtlAttrezzatura model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlAttrezzatura();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(Yii::$app->request->get('idorganizzazione')) $model->idorganizzazione = Yii::$app->request->get('idorganizzazione');

        if(Yii::$app->request->get('idsede')) $model->idsede = Yii::$app->request->get('idsede');

        if(Yii::$app->request->get('idautomezzo')) $model->idautomezzo = Yii::$app->request->get('idautomezzo');

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UtlAttrezzatura model.
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
     * Deletes an existing UtlAttrezzatura model.
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
            $data[] = ['id' => $sede->id, 'text' => $sede->indirizzo];
        }
        return ['data' => $data];
    }

    /**
     * Popola la select
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionPopulateOrgSedeAutomezzi($org, $sed)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $automezzi = UtlAutomezzo::find();
        if(Yii::$app->request->get('org')) $automezzi->andWhere(['idorganizzazione'=>Yii::$app->request->get('org')]);
        if(Yii::$app->request->get('sed')) $automezzi->andWhere(['idsede'=>Yii::$app->request->get('sed')]);
        if(!$org && !$sed) $automezzi->andWhere("0=1");
        $list = $automezzi->all();
        
        $data = [['id'=>'','text'=>'']];
        foreach ($list as $automezzo) {
            $data[] = ['id' => $automezzo->id, 'text' => $automezzo->targa];
        }
        return ['data' => $data];
    }

    /**
     * Finds the UtlAttrezzatura model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlAttrezzatura the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlAttrezzatura::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
