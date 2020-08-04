<?php

namespace backend\controllers;

use Yii;
use common\models\RubricaGroup;
use common\models\RubricaGroupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\ViewRubricaSearch;
use common\models\ViewRubrica;
use yii\web\Response;
/**
 * RubricaGroupController implements the CRUD actions for RubricaGroup model.
 */
class RubricaGroupController extends Controller
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
                        'verbs' => ['GET'],
                        'permissions' => ['listRubricaGroup']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'verbs' => ['POST'],
                        'permissions' => ['updateRubricaGroup']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createRubricaGroup']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update' ,'load-rubrica-group', 'add-single-contact', 'remove-single-contact', 'edit-multiple-contacts'],
                        'permissions' => ['updateRubricaGroup']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteRubricaGroup']
                    ],                    
                ]
            ],
        ];
    }

    /**
     * Lists all RubricaGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RubricaGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RubricaGroup model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new ViewRubricaSearch();
        $dataProvider = $searchModel->searchGroup(Yii::$app->request->queryParams);

        // sto aggiungendo o togliendo contatti
        if(Yii::$app->request->method == 'POST') {
            
            $res = $searchModel->searchGroup(Yii::$app->request->queryParams, false);
            $data = Yii::$app->request->post();
            $action = $data['action'];
            
            // action = 'check'
            if(!empty($data['check'])) {
                if($data['check'] == 'selected_all') {
                    $results = $res->query->all();
                    if($action == 'add') {
                        foreach ($results as $contatto) {
                            $model->link('contatto', $contatto);
                        }
                    } else {
                        foreach ($results as $contatto) {
                            $model->unlink('contatto', $contatto, true);
                        }
                    }

                } else {
                    $ids = explode(",",$data['check']);
                    foreach ($ids as $params) {
                        $el = explode("|",$params);
                        $contatto = ViewRubrica::find()->where(['id_riferimento'=>$el[0]])->andWhere(['tipo_riferimento'=>$el[1]])->one();
                        if($action == 'add') {
                            $model->link('contatto', $contatto);
                        } else {
                            $model->unlink('contatto', $contatto, true);
                        }
                    }
                }
            }
           
        }

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Caricamento contatti da inserire in rubrica
     * 
     * per ogni contatto aggiunge un parametro inserted da usare per sapere se un contatto è nel gruppo
     * 
     * @return [type] [description]
     */
    public function actionLoadRubricaGroup() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $gruppo = RubricaGroup::findOne( Yii::$app->request->get('id') );

        return ViewRubrica::find()
        ->select(['exists (SELECT id FROM con_rubrica_group_contact WHERE 
            con_rubrica_group_contact.id_rubrica_contatto = t.id_riferimento AND 
            con_rubrica_group_contact.tipo_rubrica_contatto = t.tipo_riferimento AND 
            con_rubrica_group_contact.id_group = '.$gruppo->id.'
        ) as inserted', new \yii\db\Expression('CONCAT("id_riferimento",\'|\',"tipo_riferimento") as id_univoco'), '*'])
        ->joinWith('specializzazioni')
        ->asArray()
        ->from(['t' => '(SELECT distinct on (id_riferimento, tipo_riferimento) * FROM view_rubrica)'])
        ->all();

    }

    /**
     * Aggiungi un singolo contatto al gruppo
     */
    public function actionAddSingleContact() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('id_univoco');
        $gruppo = RubricaGroup::findOne( Yii::$app->request->post('id_gruppo') );

        $_contact = explode("|", $data);
        $contact = ViewRubrica::find()->where(['id_riferimento'=>$_contact[0]])->andWhere(['tipo_riferimento'=>$_contact[1]])->one();

        if($contact) {
            $gruppo->link('contatto', $contact);
        }

        return ['message'=>'ok'];

    }

    /**
     * Rimuovi un singolo contatto al gruppo
     */
    public function actionRemoveSingleContact() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('id_univoco');
        $gruppo = RubricaGroup::findOne( Yii::$app->request->post('id_gruppo') );

        $_contact = explode("|", $data);
        $contact = ViewRubrica::find()->where(['id_riferimento'=>$_contact[0]])->andWhere(['tipo_riferimento'=>$_contact[1]])->one();

        if($contact) {
            $gruppo->unlink('contatto', $contact);
        }

        return ['message'=>'ok'];

    }

    public function actionEditMultipleContacts() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $gruppo = RubricaGroup::findOne( Yii::$app->request->post('id_gruppo') );
        $action = Yii::$app->request->post('action');
        $contacts = Yii::$app->request->post('contacts');
        

        if($contacts == 'all') {
            $contacts = ViewRubrica::find()
                ->from(['t' => '(SELECT distinct on (id_riferimento, tipo_riferimento) * FROM view_rubrica)'])
                ->all();
            foreach ($contacts as $contact) {
                if($action == 'add') {
                    $gruppo->link('contatto', $contact);
                } else {
                    $gruppo->unlink('contatto', $contact, true);
                }
            }
        } else {
            $contacts = json_decode($contacts, true);
            
            foreach ($contacts as $data) {

                $_contact = explode("|", $data);
                $contact = ViewRubrica::find()
                ->from(['t' => '(SELECT distinct on (id_riferimento, tipo_riferimento) * FROM view_rubrica)'])
                ->where(['id_riferimento'=>$_contact[0]])->andWhere(['tipo_riferimento'=>$_contact[1]])->one();
                if($action == 'add') {
                    $gruppo->link('contatto', $contact);
                } else {
                    $gruppo->unlink('contatto', $contact, true);
                }
            }
        }

        return ['message'=>'ok'];
    }

    /**
     * Creates a new RubricaGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RubricaGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RubricaGroup model.
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
     * Deletes an existing RubricaGroup model.
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
     * Finds the RubricaGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RubricaGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RubricaGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}
