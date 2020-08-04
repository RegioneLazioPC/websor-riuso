<?php

namespace backend\controllers;

use Yii;
use common\models\UtlAutomezzo;
use common\models\UtlAutomezzoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\Response;
use common\models\VolSede;
use common\models\VolOrganizzazione;

use common\models\UtlAttrezzatura;
use common\models\UtlAttrezzaturaSearch;
/**
 * AutomezzoController implements the CRUD actions for UtlAutomezzo model.
 */
class AutomezzoController extends Controller
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
                        'permissions' => ['viewAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteAutomezzo']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['populate-org-sedi'],
                        'permissions' => ['@']
                    ]
                ]        
            ]
        ];
    }

    /**
     * Lists all UtlAutomezzo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlAutomezzoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UtlAutomezzo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new UtlAttrezzaturaSearch();

        $params = ( isset(Yii::$app->request->queryParams['UtlAttrezzaturaSearch']) ) ? 
        ['UtlAttrezzaturaSearch'=> array_merge(Yii::$app->request->queryParams['UtlAttrezzaturaSearch'],['idautomezzo'=>$id])] : 
        ['UtlAttrezzaturaSearch'=>['idautomezzo'=>$id]];
        $dataProvider = $searchModel->search($params);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new UtlAutomezzo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlAutomezzo();
        
        if(Yii::$app->request->method == 'POST') :
            $datas = Yii::$app->request->post();     
            // formatto le date per postgresql  
            if($datas['UtlAutomezzo']['data_immatricolazione']) :   
                $data_immatricolazione = \DateTime::createFromFormat('d-m-Y', $datas['UtlAutomezzo']['data_immatricolazione']);
                $datas['UtlAutomezzo']['data_immatricolazione'] = $data_immatricolazione->format('Y-m-d');
            endif;
        endif;

        if (Yii::$app->request->method == 'POST' && $model->load($datas) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // per creazione generata da view sede
        if(Yii::$app->request->get('idsede')) :
            $sede = VolSede::find()->where(['id'=>Yii::$app->request->get('sede')])->one();
            if($sede) :
                $model->idsede = $sede->id;
                $model->idorganizzazione = $sede->id_organizzazione;
            endif;
        endif;

        // per creazione generata da view organizzazione
        if(Yii::$app->request->get('idorganizzazione')) :
            $organizzazione = VolOrganizzazione::find()->where(['id'=>Yii::$app->request->get('organizzazione')])->one();
            if($organizzazione) :
                $model->idorganizzazione = $organizzazione->id;
            endif;
        endif;

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UtlAutomezzo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->method == 'POST') :
            $datas = Yii::$app->request->post();     
            // formatto le date per postgresql     
            if($datas['UtlAutomezzo']['data_immatricolazione']) :   
                $data_immatricolazione = \DateTime::createFromFormat('d-m-Y', $datas['UtlAutomezzo']['data_immatricolazione']);
                $datas['UtlAutomezzo']['data_immatricolazione'] = $data_immatricolazione->format('Y-m-d');
            endif;
        endif;

        if (Yii::$app->request->method == 'POST' && $model->load($datas) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UtlAutomezzo model.
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
     * Finds the UtlAutomezzo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlAutomezzo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlAutomezzo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
