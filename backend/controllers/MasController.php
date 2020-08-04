<?php

namespace backend\controllers;

use Yii;

use common\models\MasMessage;
use common\models\MasMessageSearch;
use common\models\MasMessageTemplate;
use common\models\MasMessageTemplateSearch;
use common\models\ViewRubrica;
use common\models\ViewRubricaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use common\models\MasRubrica;
use common\models\UtlAnagrafica;
use common\models\utility\UtlContatto;
use common\models\utility\UtlIndirizzo;
use common\models\RubricaGroup;
use common\models\RubricaGroupSearch;
use common\models\AlmAllertaMeteo;

use common\models\MasInvio;
use common\models\MasSingleSend;

use common\models\ConMasInvioContact;
use common\models\ConMasInvioContactSearch;

use yii\db\Query;
use yii\db\Expression;

use kartik\mpdf\Pdf;
use yii\web\Response;
use yii\helpers\Url;
/**
 * MasController implements the CRUD actions for MassMessage.
 */
class MasController extends Controller
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
                    'deleteTemplate' => ['POST'],
                    'delete' => ['POST']
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user){
                        Yii::error(json_encode( Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId()) ));
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index-template', 'view-template'],
                        'permissions' => ['listMasTemplate']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-template'],
                        'permissions' => ['createMasTemplate']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-template'],
                        'permissions' => ['updateMasTemplate']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-template'],
                        'permissions' => ['deleteMasTemplate']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['listMasMessage']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createMasMessage']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-invio','view-invio', 'resend', 'reset-invio', 'send'],
                        'permissions' => ['sendMasMessage']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'update-message-channels'],
                        'permissions' => ['updateMasMessage']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteMasMessage']
                    ],  
                    [
                        'allow' => true,
                        'actions' => ['index-rubrica', 'view-rubrica'],
                        'permissions' => ['listMasRubrica']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-rubrica'],
                        'permissions' => ['createMasRubrica']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-rubrica'],
                        'permissions' => ['updateMasRubrica']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-rubrica'],
                        'permissions' => ['deleteMasRubrica']
                    ], 
                    [
                        'allow' => true,
                        'actions' => ['add-contatto-rubrica'],
                        'permissions' => ['createMasRubrica']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-contatto-rubrica'],
                        'permissions' => ['deleteMasRubrica']
                    ], 
                    [
                        'allow' => true,
                        'actions' => ['set-mobile','set-default','set-use-type'],
                        'permissions' => ['updateMasRubrica']
                    ], 
                    [
                        'allow' => true,
                        'actions' => ['create-invio', 'view-invio', 'export-invio',
                            'rubrica-list-service', 'gruppi-list-service', 'template-preview',
                            'add-destinatari-to-invio', 'add-gruppi-to-invio', 'send-to-mas'
                        ],
                        'permissions' => ['sendMasMessage']
                    ],                                
                ],

            ],
        ];
    }

    /**
     * Lists all MassMessageTemplate models.
     * @return mixed
     */
    public function actionIndexTemplate()
    {
        $searchModel = new MasMessageTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-template', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MasMessageTemplate model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewTemplate($id)
    {
        return $this->render('view-template', [
            'model' => $this->findModelTemplate($id),
        ]);
    }

    /**
     * Creates a new MasMessageTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateTemplate()
    {
        $model = new MasMessageTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-template', 'id' => $model->id]);
        }

        return $this->render('create-template', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MasMessageTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateTemplate($id)
    {
        $model = $this->findModelTemplate($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-template', 'id' => $model->id]);
        }

        return $this->render('update-template', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MasMessageTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteTemplate($id)
    {
        $this->findModelTemplate($id)->delete();

        return $this->redirect(['index-template']);
    }

    /**
     * Finds the MasMessageTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MasMessageTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelTemplate($id)
    {
        if (($model = MasMessageTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all MasMessage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MasMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MasMessage model.
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
     * Creates a new MasMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MasMessage();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }



        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing MasMessage model.
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
     * Deletes an existing MasMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->removeFromEverbridge();
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MasMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MasMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MasMessage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }







    

    


    /**
     * Lists all ViewRubrica models.
     * @return mixed
     */
    public function actionIndexRubrica()
    {
        $searchModel = new ViewRubricaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-rubrica', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionViewRubrica( $id_riferimento, $tipo_riferimento ) 
    {
        $models = ViewRubrica::find()->where(['id_riferimento'=>$id_riferimento,'tipo_riferimento'=>$tipo_riferimento])->all();

        if(!$models || count($models) == 0) throw new NotFoundHttpException("Contatto della rubrica non trovato");
        
        return $this->render('view-rubrica',[
            'model' => $models[0],
            'contacts' => $models,
            'dataProvider' => (new ViewRubricaSearch)->search(['ViewRubricaSearch'=>[
                'id_riferimento'=>$id_riferimento,
                'tipo_riferimento'=>$tipo_riferimento
            ]])
        ]);
    }

    public function actionCreateRubrica() 
    {
        $model = new MasRubrica;
        $anagrafica = new UtlAnagrafica;
        $contatto = new UtlContatto;
        $indirizzo = new UtlIndirizzo;
        $indirizzo->scenario = UtlIndirizzo::SCENARIO_ADD_RUBRICA;

        if(Yii::$app->request->method == 'POST') :
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();

            $post_data = Yii::$app->request->post();
            try {

                if(empty($post_data['UtlAnagrafica']['codfiscale'])) {
                    Yii::$app->session->setFlash('error', "Inserisci il codice fiscale");
                    throw new \Exception("Inserisci il codice fiscale", 1);
                }

                $ana = UtlAnagrafica::find()->where(['codfiscale'=>@$post_data['UtlAnagrafica']['codfiscale']])->one();
                if(!$ana) $ana = new UtlAnagrafica;

                if(empty($ana->id_sync)) :
                    $ana->load($post_data);
                    if(!$ana->save()) throw new \Exception("Errore anagrafica " . json_encode($ana->getErrors()), 1);
                endif;

                $contatto = new UtlContatto;
                $contatto->load($post_data);
                if(!$contatto->save()) throw new \Exception("Errore contatto", 1);
                
                $indirizzo = $ana->getIndirizzo()
                ->where(['civico'=>$post_data['UtlIndirizzo']['civico']])
                ->andWhere(['indirizzo'=>$post_data['UtlIndirizzo']['indirizzo']])
                ->one();

                if(!$indirizzo) {
                    $indirizzo = new UtlIndirizzo;
                    $indirizzo->scenario = UtlIndirizzo::SCENARIO_ADD_RUBRICA;
                    $indirizzo->load($post_data);
                    if(!$indirizzo->save()) throw new \Exception("Errore indirizzo", 1);
                }

                $model = MasRubrica::find()->where(['id_anagrafica'=>$ana->id])->one();

                if(!$model) $model = new MasRubrica;
                
                $model->load($post_data);
                $model->id_anagrafica = $ana->id;
                $model->id_indirizzo = $indirizzo->id;
                if(!$model->save()) throw new \Exception("Errore rubrica", 1);               
                
                // non posso linkare il contatto all'anagrafica, si rischiano conflitti con dati 
                // provenienti da altre info
                // lo linko al mas_rubrica
                $model->link('contatto', $contatto, ['use_type'=>$post_data['UtlContatto']['use_type']]); 

                $dbTrans->commit();

                $model->syncEverbridge();

                return $this->redirect(['view-rubrica', 'id_riferimento' => $model->id, 'tipo_riferimento' => 'id_mas_rubrica']);
            } catch( \Exception $e) {
                $dbTrans->rollBack();
                throw $e;
            }
            
        endif;

        
        return $this->render('create-rubrica', [
            'model' => $model,
            'anagrafica' => $anagrafica,
            'contatto' => $contatto,
            'indirizzo' => $indirizzo
        ]);
        
    }

    public function actionUpdateRubrica($id) 
    {
        
        $model = MasRubrica::find()->where(['id'=>$id])->one();
        if(!$model) throw new NotFoundHttpException("Elemento non trovato");
        
        $anagrafica = $model->anagrafica;
        $indirizzo = $model->indirizzo;
        $indirizzo->scenario = UtlIndirizzo::SCENARIO_ADD_RUBRICA;

        if(Yii::$app->request->method == 'POST') :
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();

            $post_data = Yii::$app->request->post();
            try {

                
                if(empty($anagrafica->id_sync)) :
                    $anagrafica->load($post_data);
                    if(!$anagrafica->save()) throw new \Exception("Errore anagrafica", 1);
                endif;

                $indirizzo->load($post_data);
                if(!$indirizzo->save()) throw new \Exception("Errore indirizzo", 1);

                $model->load($post_data);
                if(!$model->save()) throw new \Exception("Errore rubrica", 1);               
                
                $dbTrans->commit();

                return $this->redirect(['view-rubrica', 'id_riferimento' => $model->id, 'tipo_riferimento' => 'id_mas_rubrica']);

            } catch( \Exception $e) {
                $dbTrans->rollBack();
                throw $e;
            }
            
        endif;

        
        return $this->render('update-rubrica', [
            'model' => $model,
            'anagrafica' => $anagrafica,
            'indirizzo' => $indirizzo
        ]);
        
    }


    /**
     * Aggiunge un contatto ad un elemento di mas_rubrica
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionAddContattoRubrica($id){

        $base_model = MasRubrica::findOne($id);
        if(!$base_model) throw new NotFoundHttpException("Elemento non trovato");
        
        $contatto = new UtlContatto;
        $contatto->load(Yii::$app->request->post());
        if($contatto->save()):
            $base_model->link('contatto', $contatto, ['use_type'=>$contatto->use_type, 'type'=>$contatto->type]);
        endif;

        $models = ViewRubrica::find()->where(['id_riferimento'=>$base_model->id,'tipo_riferimento'=>'id_mas_rubrica'])->all();

        if(!$models || count($models) == 0) throw new NotFoundHttpException("Contatto della rubrica non trovato");

        $base_model->syncEverbridge();
        
        return $this->redirect(['view-rubrica','id_riferimento'=>$base_model->id,'tipo_riferimento'=>'id_mas_rubrica']);

    }

    /**
     * Elimina un contatto di un elemento di rubrica
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id_contatto      [description]
     * @param  [type] $contatto_type    [description]
     * @return [type]                   [description]
     */
    public function actionDeleteContattoRubrica(
        $id_riferimento,
        $tipo_riferimento,
        $id_contatto,
        $contatto_type
    ){
        $models = ViewRubrica::find()->where([
            'id_riferimento' => $id_riferimento,
            'tipo_riferimento' => $tipo_riferimento
        ])->all();

        $element = ViewRubrica::find()->where([
            'id_riferimento' => $id_riferimento,
            'tipo_riferimento' => $tipo_riferimento,
            'id_contatto' => $id_contatto,
            'contatto_type' => $contatto_type,
        ])->one();

        if(!$element) throw new NotFoundHttpException("Contatto non trovato");
        
        // cancella solo i contatti!
        if($element && $contatto_type == 'con_mas_rubrica_contatto') {
            $contatto = UtlContatto::findOne($id_contatto);
            if($contatto) $contatto->delete();

            $base_model = MasRubrica::findOne($id_riferimento);
            if(!$base_model) throw new NotFoundHttpException("Elemento non trovato");
            
            $base_model->syncEverbridge();
        }

        // se era l'ultimo contatto non ce ne sono più
        if(count($models) == 0) throw new NotFoundHttpException("Contatto non trovato");

        
        return $this->redirect(['view-rubrica', 'id_riferimento'=>$models[0]->id_riferimento, 'tipo_riferimento'=>$models[0]->tipo_riferimento]);
    }

    /**
     * Imposta contatto predefinito o no
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id_contatto      [description]
     * @param  [type] $contatto_type    [description]
     * @param  [type] $value            [description]
     * @return [type]                   [description]
     */
    public function actionSetDefault(
        $id_riferimento,
        $tipo_riferimento,
        $id_contatto,
        $contatto_type,
        $value
    ){        

        $element = ViewRubrica::find()->where([
            'id_riferimento' => $id_riferimento,
            'tipo_riferimento' => $tipo_riferimento,
            'id_contatto' => $id_contatto,
            'contatto_type' => $contatto_type,
        ])->one();

        if(!$element) throw new NotFoundHttpException("Contatto non trovato");
        
        // cancella solo i contatti
        if($element && $contatto_type == 'con_mas_rubrica_contatto') {
            $contatto = UtlContatto::findOne($id_contatto);
            if($contatto) {
                $contatto->check_predefinito = $value;
                $contatto->save();
            }

            $base_model = MasRubrica::findOne($id_riferimento);
            if(!$base_model) throw new NotFoundHttpException("Elemento non trovato");
            
            $base_model->syncEverbridge();
        }

        
        return $this->redirect(['view-rubrica', 'id_riferimento'=>$element->id_riferimento, 'tipo_riferimento'=>$element->tipo_riferimento]);
    }

    /**
     * Imposta contatto predefinito o no
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id_contatto      [description]
     * @param  [type] $contatto_type    [description]
     * @param  [type] $value            [description]
     * @return [type]                   [description]
     */
    public function actionSetMobile(
        $id_riferimento,
        $tipo_riferimento,
        $id_contatto,
        $contatto_type,
        $value
    ){  

        $element = ViewRubrica::find()->where([
            'id_riferimento' => $id_riferimento,
            'tipo_riferimento' => $tipo_riferimento,
            'id_contatto' => $id_contatto,
            'contatto_type' => $contatto_type,
        ])->one();

        if(!$element) throw new NotFoundHttpException("Contatto non trovato");
        
        // cancella solo i contatti!
        if($element && $contatto_type == 'con_mas_rubrica_contatto') {
            $contatto = UtlContatto::findOne($id_contatto);
            if($contatto) {
                $contatto->check_mobile = $value;
                $contatto->save();
            }

            $base_model = MasRubrica::findOne($id_riferimento);
            if(!$base_model) throw new NotFoundHttpException("Elemento non trovato");
            
            $base_model->syncEverbridge();
        }

        

        return $this->redirect(['view-rubrica', 'id_riferimento'=>$element->id_riferimento, 'tipo_riferimento'=>$element->tipo_riferimento]);
    }

    /**
     * Imposta contatto messaggistica o allertamento
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id_contatto      [description]
     * @param  [type] $contatto_type    [description]
     * @param  [type] $value            [description]
     * @return [type]                   [description]
     */
    public function actionSetUseType(
        $id_riferimento,
        $tipo_riferimento,
        $id_contatto,
        $contatto_type,
        $value
    ){
        
        $element = ViewRubrica::find()->where([
            'id_riferimento' => $id_riferimento,
            'tipo_riferimento' => $tipo_riferimento,
            'id_contatto' => $id_contatto,
            'contatto_type' => $contatto_type,
        ])->one();

        if(!$element) throw new NotFoundHttpException("Contatto non trovato");
        
        // cancella solo i contatti!
        if($element && $contatto_type == 'con_mas_rubrica_contatto') {

            $contatto = UtlContatto::findOne($id_contatto);
            
            if($contatto) {
                $contatto->use_type = $value;
                $contatto->save();

                $connection = Yii::$app->getDb();
                $command = $connection->createCommand("
                    UPDATE con_mas_rubrica_contatto SET use_type = :use_type
                    WHERE id_contatto = :id_contatto AND id_mas_rubrica = :id_riferimento", [
                        ':use_type' => $value, 
                        ':id_contatto' => $contatto->id,
                        ':id_riferimento' => $element->id_riferimento
                    ]);

                $result = $command->queryAll();
                
                
            }

            $base_model = MasRubrica::findOne($id_riferimento);
            if(!$base_model) throw new NotFoundHttpException("Elemento non trovato");
            
            $base_model->syncEverbridge();
        }

        

        return $this->redirect(['view-rubrica', 'id_riferimento'=>$element->id_riferimento, 'tipo_riferimento'=>$element->tipo_riferimento]);
    }

    /**
     * Scelta destinatari contatti e gruppi con filtro
     * 
     * splitta in 2 la pagina, a sinistra i gruppi non paginati, usa la selezione per determinare quali usare
     * a destra i contatti tutti quelli che vengono selezionati li usa
     * torno 2 data provider con contatti e gruppi
     * @deprecated
     * @param  [type] $id_messaggio [description]
     * @return [type]               [description]
     */
    public function actionCreateInvio($id_messaggio) {

        $model = $this->findModel($id_messaggio);
        if(!$model) throw new NotFoundHttpException("Risorsa non trovata");

        $searchContactModel = new ViewRubricaSearch();
        $dataContactProvider = $searchContactModel->searchGroup(Yii::$app->request->queryParams);

        $searchGroupModel = new RubricaGroupSearch();
        $dataGroupProvider = $searchGroupModel->search(Yii::$app->request->queryParams);

        // per evitare conflitti in paginazione
        $dataContactProvider->pagination->pageParam = 'first-dp-page';
        $dataContactProvider->sort->sortParam = 'first-dp-sort';

        $dataGroupProvider->pagination->pageParam = 'second-dp-page';
        $dataGroupProvider->sort->sortParam = 'second-dp-sort';


        if(Yii::$app->request->method == 'POST') {
            // creo transazione
            
            
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();

            
            try {
                

                $invio = new MasInvio;
                $invio->id_message = $model->id;
                $invio->channel_mail = $model->channel_mail;
                $invio->channel_pec = $model->channel_pec;
                $invio->channel_push = $model->channel_push;
                $invio->channel_sms = $model->channel_sms;
                $invio->channel_fax = $model->channel_fax;
                $invio->data_invio = date("Y-m-d H:m:s", time());

                if(!$invio->save()) throw new \Exception("Dati invio non validi");

                
                $data = Yii::$app->request->post();
                $contatti = [];
                
                
                /**
                 * Permetti inserimento contatti solo se il canale è previsto
                 * @var array
                 */
                $avaible_tipo_contatto = [];
                if($invio->channel_mail == 1) $avaible_tipo_contatto[] = 0;
                if($invio->channel_pec == 1) $avaible_tipo_contatto[] = 1;
                if($invio->channel_sms == 1) :
                    $avaible_tipo_contatto[] = 2;
                    $avaible_tipo_contatto[] = 4;
                endif;
                if($invio->channel_fax == 1) :
                    $avaible_tipo_contatto[] = 3;
                    $avaible_tipo_contatto[] = 5;
                endif;
                if($invio->channel_push == 1) $avaible_tipo_contatto[] = 6;


                $group_ids = [];
                if(!empty($data['group_check'])) {
                    $res_groups = $searchGroupModel->search(Yii::$app->request->queryParams, false);
                    if($data['group_check'] == 'selected_all') {
                        $results = $res_groups->query->all();
                        
                        foreach ($results as $gruppo) {
                            // linko all'invio
                            $group_ids[] = $gruppo->id;

                        }
                        
                    } else {
                        
                        $ids = explode(",",$data['group_check']);
                        $group_ids = $ids;
                        
                    }
                }

                $params = Yii::$app->request->queryParams;
                if(!empty($group_ids)) $params['groups'] = $group_ids;

                if(!empty($avaible_tipo_contatto)) $params['avaible_tipo_contatto'] = $avaible_tipo_contatto;
                
                $query = $searchContactModel->search($params, false);
                if(!empty($data['check'])) {
                    
                    if($data['check'] != 'selected_all') {
                    
                        $ids = explode(",",$data['check']);                        
                        
                        $query->query->where('id_riferimento is null');
                        foreach ($ids as $params) {

                            $el = explode("|",$params);                            
                            $query->query->orWhere(['id_riferimento'=>$el[0], 'tipo_riferimento'=>$el[1] ]);
                        }
                       
                    } else {
                       
                    }

                } 

                

                $query->query->select(['id_riferimento','tipo_riferimento','valore_contatto','valore_riferimento']);
                $query->query->addSelect(new Expression(intval($invio->id).' as id_invio'));

                $query->query->addSelect(
                    'CASE
                      WHEN (tipo_contatto = 0) THEN \'Email\'
                      WHEN (tipo_contatto = 1) THEN \'Pec\'
                      WHEN (tipo_contatto = 2 AND check_mobile = 1) THEN \'Sms\'
                      WHEN (tipo_contatto = 4 AND check_mobile = 1) THEN \'Sms\'
                      WHEN (tipo_contatto = 3) THEN \'Fax\'
                      WHEN (tipo_contatto = 5) THEN \'Fax\'
                      WHEN (tipo_contatto = 6) THEN \'Push\'
                     END AS channel'
                );

                // per allerta solo use_type = 1;
                if(!empty($model->id_allerta)) {
                    $query->query->andWhere('use_type = 1');
                } else {
                    $query->query->andWhere('use_type = 0');
                }

                $query->query->andWhere('CASE 
                        WHEN (tipo_contatto = 2 OR tipo_contatto = 4) THEN check_mobile = 1
                        ELSE 1 = 1
                     END');

                $query->query->addSelect(['vendor']);

                $query->query->andWhere(['tipo_contatto'=>$avaible_tipo_contatto]);
                $q_s = $query->query->createCommand()->getRawSql();
                
                

                echo Yii::$app->db->createCommand(
                    'INSERT INTO con_mas_invio_contact(id_rubrica_contatto, tipo_rubrica_contatto, valore_rubrica_contatto, valore_riferimento, id_invio, channel, vendor) '.$q_s
                )->execute();

                
                $dbTrans->commit();


                /**
                 * Invio al dispatcher tutti i contatti
                 * @var [type]
                 */
                $contatti = ConMasInvioContact::find()
                ->select([
                'id','id_rubrica_contatto','tipo_rubrica_contatto','valore_rubrica_contatto','channel','vendor','valore_riferimento'
                ])
                ->addSelect(new Expression(intval($invio->id).' as id_invio'))
                ->addSelect(new Expression('0 as status'))
                ->addSelect(new Expression(time().' as created_at'))
                ->addSelect(new Expression(time().' as updated_at'))
                ->where(['id_invio'=>$invio->id]);


                $q_s = $contatti->createCommand()->getRawSql();
                $all_contacts = $contatti->asArray()->all();

                $dispatch = new \common\utils\MasDispatcher( $all_contacts, $invio );
                $res = $dispatch->initialize();

                

                return $this->redirect(['view-invio', 'id_invio'=>$invio->id]);
                
                
            } catch(\Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create-invio', [
                    'model' => $model,
                    'searchContactModel' => $searchContactModel,
                    'dataContactProvider' => $dataContactProvider,
                    'searchGroupModel' => $searchGroupModel,
                    'dataGroupProvider' => $dataGroupProvider
                ]);
            }

        }

        return $this->render('create-invio', [
            'model' => $model,
            'searchContactModel' => $searchContactModel,
            'dataContactProvider' => $dataContactProvider,
            'searchGroupModel' => $searchGroupModel,
            'dataGroupProvider' => $dataGroupProvider
        ]);
    }

    

    /**
     * View invio con elenco single send
     * @param  [type] $id_invio [description]
     * @return [type]     [description]
     */
    public function actionViewInvio($id_invio) 
    {
        $model = MasInvio::findOne($id_invio);
        
        return $this->render('view-invio', [
            'model'=>$model
        ]);
    }





    public function actionExportInvio( $channel, $id_invio, $result_type = 'csv' ) {

        $channels = ['Push','Email','Fax','Pec','Sms'];
        if(!in_array($channel, $channels)) throw new \yii\web\HttpException(422, 'Canale non valido');
        
        $const_mas_status = ($channel == 'Pec' || $channel == 'Fax' || $channel == 'Sms') ? 
        MasMessage::STATUS_RECEIVED : MasMessage::STATUS_SEND;

        $invio = MasInvio::findOne($id_invio);
        if(!$invio) throw new \yii\web\HttpException(404, 'Invio non trovato');

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT 
            con_mas_invio_contact.valore_rubrica_contatto, 
            con_mas_invio_contact.valore_riferimento,  
            con_mas_invio_contact.channel, 
            (SELECT count(mas_single_send.id) FROM mas_single_send WHERE 
            id_invio = :id_invio AND 
            mas_single_send.id_con_mas_invio_contact = con_mas_invio_contact.id AND
            mas_single_send.status in 
            (".new \yii\db\Expression( implode(",", $const_mas_status) ).")) as inviato,
                json_agg(json_build_object( 'status', mas_single_send.status, 'sent', mas_single_send.sent_time, 'feedback', mas_single_send.feedback_time ) ) as invii
                FROM con_mas_invio_contact
                LEFT JOIN mas_single_send on mas_single_send.id_con_mas_invio_contact = con_mas_invio_contact.id AND mas_single_send.id_invio = con_mas_invio_contact.id_invio
                 WHERE con_mas_invio_contact.id_invio = :id_invio
                 AND con_mas_invio_contact.channel = :channel
                 GROUP by con_mas_invio_contact.valore_rubrica_contatto, con_mas_invio_contact.valore_riferimento, con_mas_invio_contact.channel, con_mas_invio_contact.id
                ;", [
            ':id_invio' => intval($invio->id),
            ':channel' => $channel
        ]);

        $result = $command->queryAll();

        switch( $result_type ) {
            case 'csv': $this->getCsvRows($result, $invio, $channel); break;
            case 'pdf': return $this->getPdfRows($result, $invio, $channel); break;
        }


    }

    /**
     * Mostra export csv
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    private function getCsvRows( $result, $invio, $channel ){
        ob_start();
        $file = fopen("php://output", 'w');

        fputcsv($file, [
            'riferimento',
            'contatto',
            'inviato',
            'stato invio',
            'data invio',
            'data feedback'
        ], "\t");
        foreach ($result as $dest) {

            $send_ = json_decode( $dest['invii'], true );
            foreach ($send_ as $row) {
                $arr = [
                    $dest['valore_riferimento'],
                    $dest['valore_rubrica_contatto'],
                    ($dest['inviato'] > 0) ? 'Si' : 'No',
                    (!empty($row['status'])) ? MasSingleSend::getStatoByNumber( $row['status'] ) : '',
                    (!empty($row['status'])) ? ((!empty($row['sent'])) ? date('d-m-Y H:i:s', $row['sent']) : '') : '',
                    (!empty($row['status'])) ? ((!empty($row['feedback'])) ? date('d-m-Y H:i:s', $row['feedback']) : '') : ''
                ];
            }
            fputcsv($file, $arr, "\t");            
            
        }

        fclose($file);
        
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: GET');
        header('Content-Encoding: UTF-8');
        header('Content-type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename=export_'.$invio->id.'_'.$channel.'.xls');
        echo ob_get_clean();

        exit();
    }

    /**
     * Mostra export pdf
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    private function getPdfRows( $result, $invio, $channel ) {
        return $this->renderPartial('export-pdf.php', [
            'result' => $result,
            'invio' => $invio,
            'channel' => $channel
        ]);
        
        
    }


    /**
     * Invia messaggio da api
     * @return [type] [description]
     */
    public function actionSend() {


        $messaggio = new MasMessage;

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        
        try {

             
            $messaggio->load(Yii::$app->request->post());
            
            if($messaggio->save()) {
                
                $files = UploadedFile::getInstances($messaggio, 'mediaFile');
                
                if(!empty($files)) {

                    $tipo = \common\models\UplTipoMedia::find()->where(
                        ['descrizione'=>'Allegato messaggio']
                    )->one();

                    if(!$tipo) {
                        $tipo = new  \common\models\UplTipoMedia;
                        $tipo->descrizione = 'Allegato messaggio';
                        $tipo->save();
                    }

                    foreach($files as $file) {
                        

                        $media = new \common\models\UplMedia;
                        $media->uploadFile($file, $tipo->id, MasMessage::validMessageMimes());
                        $media->refresh();
                        
                        $messaggio->link('file', $media);
                        
                    }
                }
                
                $invio = new MasInvio;
                $invio->id_message = $messaggio->id;
                $invio->channel_mail = $messaggio->channel_mail;
                $invio->channel_pec = $messaggio->channel_pec;
                $invio->channel_push = $messaggio->channel_push;
                $invio->channel_sms = $messaggio->channel_sms;
                $invio->channel_fax = $messaggio->channel_fax;
                $invio->data_invio = date("Y-m-d H:m:s", time());

                if(!$invio->save()) throw new \Exception("Dati invio non validi");

                /**
                 * @todo  from here
                 */
                $dbTrans->commit();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'id_invio' => $invio->id,
                    'redirect_url' => Url::to(['mas/view-invio', 'id_invio' => $invio->id])
                ];

                
            } else {
                throw new \Exception($messaggio->getErrors(), 1);                
            }
            

        } catch (\Exception $e) {
            $dbTrans->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            
            return $e->getMessage();
        }
    }

    /**
     * Servizi http per lista rubrica
     * @return [type] [description]
     */
    public function actionRubricaListService( ) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();

        return ViewRubrica::find()
        ->asArray()
        ->from(['t' => '(SELECT distinct on (id_riferimento, tipo_riferimento) * FROM view_rubrica)'])
        ->all();

    }

    /**
     * Servizi http per gruppi
     * @return [type] [description]
     */
    public function actionGruppiListService( ) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();

        return RubricaGroup::find()->all();

    }

    /**
     * Servizio per preview template
     * @return [type] [description]
     */
    public function actionTemplatePreview( ) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();

        return MasMessageTemplate::findOne($params['id']);

    }

    /**
     * Aggiungi destinatari all'invio
     * @return [type] [description]
     */
    public function actionAddDestinatariToInvio() {
        
        ini_set('xdebug.max_nesting_level', 10000);

        Yii::$app->response->format = Response::FORMAT_JSON;
        

        $invio = MasInvio::findOne(Yii::$app->request->post('id_invio'));

        $avaible_tipo_contatto = [];
        if($invio->channel_mail == 1) $avaible_tipo_contatto[] = 0;
        if($invio->channel_pec == 1) $avaible_tipo_contatto[] = 1;
        if($invio->channel_sms == 1) :
            $avaible_tipo_contatto[] = 2;
            $avaible_tipo_contatto[] = 4;
        endif;
        if($invio->channel_fax == 1) :
            $avaible_tipo_contatto[] = 3;
            $avaible_tipo_contatto[] = 5;
        endif;
        if($invio->channel_push == 1) $avaible_tipo_contatto[] = 6;

        $destinatari = Yii::$app->request->post('contacts');
        $use_type = !empty($invio->message->allerta) ? 2 : [0,2]; // editing, i messaggi normali vanno su entrambi i canali [0,2]
        $search = ViewRubricaSearch::find()
        ->where([ 'tipo_contatto'=>$avaible_tipo_contatto ]);
        
        if($destinatari == 'all') {

        } else {
            $ids = explode( ",", $destinatari );
            $ors = [];
            $ors[] = 'or';

            
            foreach ($ids as $params) {

                $el = explode("|",$params);
                $ors[] = ['id_riferimento'=>$el[0], 'tipo_riferimento'=>$el[1] ];                            
                
            }

            $search->andWhere( $ors );
        }

        $search->select([
            'id_riferimento','tipo_riferimento','valore_contatto','valore_riferimento','ext_id','everbridge_identifier'
        ]);
        $search->addSelect(new Expression(intval($invio->id).' as id_invio'));

        $search->addSelect(
            'CASE
              WHEN (tipo_contatto = 0) THEN \'Email\'
              WHEN (tipo_contatto = 1) THEN \'Pec\'
              WHEN (tipo_contatto = 2 AND check_mobile = 1) THEN \'Sms\'
              WHEN (tipo_contatto = 4 AND check_mobile = 1) THEN \'Sms\'
              WHEN (tipo_contatto = 3) THEN \'Fax\'
              WHEN (tipo_contatto = 5) THEN \'Fax\'
              WHEN (tipo_contatto = 6) THEN \'Push\'
             END AS channel'
        );
        $search->addSelect(['vendor']);
        $search
        ->andWhere('CASE 
            WHEN (tipo_contatto = 2 OR tipo_contatto = 4) THEN check_mobile = 1
            ELSE 1 = 1
         END')
        ->andWhere(['use_type'=>$use_type])
        ->andWhere( [ 'tipo_contatto' => $avaible_tipo_contatto ] );

        $plain = $search->createCommand()->getRawSql();

        Yii::$app->db->createCommand(
            'INSERT INTO con_mas_invio_contact(id_rubrica_contatto, tipo_rubrica_contatto, valore_rubrica_contatto, valore_riferimento, ext_id, everbridge_identifier, id_invio, channel, vendor) ' . $plain . ' ON CONFLICT (id_rubrica_contatto,tipo_rubrica_contatto,valore_rubrica_contatto,id_invio) DO NOTHING '
        )->execute();

        return [
            'message'=>'ok'
        ];

    }


    /**
     * Aggiungi gruppi di destinatari all'invio
     * @return [type] [description]
     */
    public function actionAddGruppiToInvio() {
        
        ini_set('xdebug.max_nesting_level', 10000);
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $invio = MasInvio::findOne(Yii::$app->request->post('id_invio'));

        $avaible_tipo_contatto = [];
        if($invio->channel_mail == 1) $avaible_tipo_contatto[] = 0;
        if($invio->channel_pec == 1) $avaible_tipo_contatto[] = 1;
        if($invio->channel_sms == 1) :
            $avaible_tipo_contatto[] = 2;
            $avaible_tipo_contatto[] = 4;
        endif;
        if($invio->channel_fax == 1) :
            $avaible_tipo_contatto[] = 3;
            $avaible_tipo_contatto[] = 5;
        endif;
        if($invio->channel_push == 1) $avaible_tipo_contatto[] = 6;

        $destinatari = Yii::$app->request->post('groups');
        $use_type = !empty($invio->message->allerta) ? 2 : [0,2]; // editing, i messaggi normali vanno su entrambi i canali [0,2]
        $search = ViewRubricaSearch::find();

        $ids = [];
        if($destinatari == 'all') {
            $groups = RubricaGroup::find()->all();
            foreach ($groups as $gr) {
                $ids[] = $gr->id;
            }
        } else {
            $_ids = explode( ",", $destinatari );
            
            foreach ($_ids as $id) {
                $ids[] = intval($id);
            }
        }

        /**
         * Inserisce una subquery
         */
        $search->andWhere('exists (SELECT 1 from con_rubrica_group_contact 
                where 
                CASE 
                  WHEN (tipo_contatto = 2 OR tipo_contatto = 4) THEN check_mobile = 1
                  ELSE 1 = 1
                END AND
                id_rubrica_contatto = id_riferimento AND 
                tipo_rubrica_contatto = tipo_riferimento AND 
                use_type = '.$use_type.' AND
                id_group IN ('.implode(",",$ids).') )'
        );

        $search->select([
            'id_riferimento','tipo_riferimento','valore_contatto','valore_riferimento','ext_id','everbridge_identifier'
        ]);
        $search->addSelect(new Expression(intval($invio->id).' as id_invio'));

        $search->addSelect(
            'CASE
              WHEN (tipo_contatto = 0) THEN \'Email\'
              WHEN (tipo_contatto = 1) THEN \'Pec\'
              WHEN (tipo_contatto = 2 AND check_mobile = 1) THEN \'Sms\'
              WHEN (tipo_contatto = 4 AND check_mobile = 1) THEN \'Sms\'
              WHEN (tipo_contatto = 3) THEN \'Fax\'
              WHEN (tipo_contatto = 5) THEN \'Fax\'
              WHEN (tipo_contatto = 6) THEN \'Push\'
             END AS channel'
        );
        $search->addSelect(['vendor']);
        $search
        ->andWhere('CASE 
            WHEN (tipo_contatto = 2 OR tipo_contatto = 4) THEN check_mobile = 1
            ELSE 1 = 1
         END')
        ->andWhere( [ 'tipo_contatto' => $avaible_tipo_contatto ] );

        $plain = $search->createCommand()->getRawSql();

        Yii::$app->db->createCommand(
            'INSERT INTO con_mas_invio_contact(id_rubrica_contatto, tipo_rubrica_contatto, valore_rubrica_contatto, valore_riferimento, ext_id, everbridge_identifier, id_invio, channel, vendor) ' . $plain . ' ON CONFLICT (id_rubrica_contatto,tipo_rubrica_contatto,valore_rubrica_contatto,id_invio) DO NOTHING '
        )->execute();

        return [
            'message'=>'ok'
        ];

    }

    /**
     * Prepara il dispatching del messaggio
     * servizio http
     * @return [type] [description]
     */
    public function actionResetInvio() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try{
            $conn = \Yii::$app->db;
            $dbTrans = $conn->beginTransaction();

            $invio = MasInvio::findOne(Yii::$app->request->get('id_invio'));
            $message = MasMessage::findOne($invio->id_message);

            if(!empty($message->id_allerta)) {
                $allerta = AlmAllertaMeteo::findOne($message->id_allerta);
                if($allerta) {
                    if(!$allerta->delete()) throw new \Exception("Allerta non eliminata", 1);
                }
            }

            if(!$invio->delete()) throw new \Exception("Invio non eliminato", 1);
            if(!$message->delete()) throw new \Exception("Messaggio non eliminato", 1);

            $dbTrans->commit();
            
        } catch(\Exception $e) {
            $dbTrans->rollBack();

            return ['error'=>$e->getMessage()];
        }

        return ['message'=>'ok'];
    }

    /**
     * Invia al modulo MAS
     * servizio http
     * @return [type] [description]
     */
    public function actionSendToMas() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try{
            $invio = MasInvio::findOne(Yii::$app->request->post('id_invio'));

            $contatti = ConMasInvioContact::find()
            ->select([
            'id','id_rubrica_contatto','tipo_rubrica_contatto','valore_rubrica_contatto','ext_id','everbridge_identifier','channel','vendor','valore_riferimento'
            ])
            ->addSelect(new Expression(intval($invio->id).' as id_invio'))
            ->addSelect(new Expression('0 as status'))
            ->addSelect(new Expression(time().' as created_at'))
            ->addSelect(new Expression(time().' as updated_at'))
            ->where(['id_invio'=>$invio->id]);


            $q_s = $contatti->createCommand()->getRawSql();
            $all_contacts = $contatti->asArray()->all();

            $dispatch = new \common\utils\MasDispatcher( $all_contacts, $invio );
            $res = $dispatch->initialize();
        } catch(\Exception $e) {
            return ['error'=>$e->getMessage()];
        }

        return ['message'=>'ok'];
    }

    /**
     * Aggiorna canali del messaggio
     * servizio http
     * @return [type] [description]
     */
    public function actionUpdateMessageChannels() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try{
            
            $message = MasMessage::findOne(Yii::$app->request->post('id_message'));
            $message->channel_mail = Yii::$app->request->post('channel_mail');
            $message->channel_pec = Yii::$app->request->post('channel_pec');
            $message->channel_fax = Yii::$app->request->post('channel_fax');
            $message->channel_sms = Yii::$app->request->post('channel_sms');
            $message->channel_push = Yii::$app->request->post('channel_push');
            if(!$message->save()) throw new \Exception( json_encode( $message->getErrors() ), 1);
            
        } catch(\Exception $e) {
            return ['error'=>$e->getMessage()];
        }

        return ['message'=>'ok'];
    }

    /**
     * Reinvia il messaggio
     * servizio http
     * @return [type] [description]
     */
    public function actionResend() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try{
            
            $message = MasMessage::findOne(Yii::$app->request->get('id_message'));
            if(!$message) throw new \Exception("Messaggio non trovato", 1);
            
            $invio = new MasInvio;
            $invio->id_message = $message->id;
            $invio->channel_mail = $message->channel_mail;
            $invio->channel_pec = $message->channel_pec;
            $invio->channel_push = $message->channel_push;
            $invio->channel_sms = $message->channel_sms;
            $invio->channel_fax = $message->channel_fax;
            $invio->data_invio = date("Y-m-d H:m:s", time());

            if(!$invio->save()) throw new \Exception("Dati invio non validi");

            return ['message'=>'ok', 'id_invio'=>$invio->id, "redirect_url" => Url::to(['mas/view-invio', 'id_invio' => $invio->id])];

        } catch(\Exception $e) {
            return ['error'=>$e->getMessage()];
        }

        
    }

    /**
     * Linka i contatti in base al canale
     * @deprecated
     * @param  [type] $invio    Il model dell'invio
     * @param  [type] $contatto Il contatto della view rubrica
     * @return [type]           [description]
     */
    private function linkContactToInvio( $invio, $contatto )
    {
        
        switch($contatto['tipo_contatto']) {
            case 0: // EMail
                if($invio->channel_mail == 1) {
                    $conn = new ConMasInvioContact;
                    $conn->id_invio = $invio->id;
                    $conn->id_rubrica_contatto = $contatto['id_riferimento'];
                    $conn->tipo_rubrica_contatto = $contatto['tipo_riferimento']; 
                    $conn->valore_rubrica_contatto = $contatto['valore_contatto']; 
                    $conn->channel = 'Email';
                    if(!$conn->save()) throw new \Exception("Errore creazione collegamento ".json_encode($conn->getErrors()), 1);
                    
                    $this->sendMessage ( $invio, $contatto, 'Email', $conn->id );   
                                    
                }
            break;
            case 1: // Pec
                if($invio->channel_pec == 1) {
                    $conn = new ConMasInvioContact;
                    $conn->id_invio = $invio->id;
                    $conn->id_rubrica_contatto = $contatto['id_riferimento'];
                    $conn->tipo_rubrica_contatto = $contatto['tipo_riferimento']; 
                    $conn->valore_rubrica_contatto = $contatto['valore_contatto']; 
                    $conn->channel = 'Pec';
                    if(!$conn->save()) throw new \Exception("Errore creazione collegamento ".json_encode($conn->getErrors()), 1);

                    
                    $this->sendMessage ( $invio, $contatto, 'Pec', $conn->id );                   
                }
            break;
            case 2: case 4:
                /**
                 * @todo  il check mobile
                 */
                if($invio->channel_sms == 1) {
                    $conn = new ConMasInvioContact;
                    $conn->id_invio = $invio->id;
                    $conn->id_rubrica_contatto = $contatto['id_riferimento'];
                    $conn->tipo_rubrica_contatto = $contatto['tipo_riferimento']; 
                    $conn->valore_rubrica_contatto = $contatto['valore_contatto']; 
                    $conn->channel = 'Sms';
                    if(!$conn->save()) throw new \Exception("Errore creazione collegamento ".json_encode($conn->getErrors()), 1);

                    
                    $this->sendMessage ( $invio, $contatto, 'Sms', $conn->id );                 
                }
            break; 
            case 3: case 5:
                if($invio->channel_fax == 1) {
                    $conn = new ConMasInvioContact;
                    $conn->id_invio = $invio->id;
                    $conn->id_rubrica_contatto = $contatto['id_riferimento'];
                    $conn->tipo_rubrica_contatto = $contatto['tipo_riferimento']; 
                    $conn->valore_rubrica_contatto = $contatto['valore_contatto']; 
                    $conn->channel = 'Fax';
                    if(!$conn->save()) throw new \Exception("Errore creazione collegamento ".json_encode($conn->getErrors()), 1);

                    
                    $this->sendMessage ( $invio, $contatto, 'Fax', $conn->id );                 
                }
            break;
            case 6:
                if($invio->channel_push == 1) {
                    $conn = new ConMasInvioContact;
                    $conn->id_invio = $invio->id;
                    $conn->id_rubrica_contatto = $contatto['id_riferimento'];
                    $conn->tipo_rubrica_contatto = $contatto['tipo_riferimento']; 
                    $conn->valore_rubrica_contatto = $contatto['valore_contatto']; 
                    $conn->channel = 'Push';
                    if(!$conn->save()) throw new \Exception("Errore creazione collegamento ".json_encode($conn->getErrors()), 1);

                    
                    $this->sendMessage ( $invio, $contatto, 'Push', $conn->id );                   
                }
            break;
        }
    }

    /**
     * @deprecated
     * @param  [type] $invio    [description]
     * @param  [type] $contatto [description]
     * @param  [type] $channel  [description]
     * @param  [type] $id       [description]
     * @return [type]           [description]
     */
    private function sendMessage ( $invio, $contatto, $channel, $id ) {
        $send = new MasSingleSend;
        $send->id_rubrica_contatto = $contatto['id_riferimento'];
        $send->tipo_rubrica_contatto = $contatto['tipo_riferimento'];
        $send->valore_rubrica_contatto = $contatto['valore_contatto'];
        $send->id_con_mas_invio_contact = $id;
        $send->channel = $channel;
        $send->id_invio = $invio->id;
        $send->status = 0;
        if(!$send->save()) throw new \Exception("Impossibile salvare il singolo invio",1);

        unset($send);
        unset($contatto);
    }

    /**
     * @deprecated
     * @param  [type]  $invio    [description]
     * @param  [type]  $contatto [description]
     * @param  [type]  $channel  [description]
     * @param  integer $stato    [description]
     * @return [type]            [description]
     */
    private function sendMessageWithConMasInvioContact ( $invio, $contatto, $channel, $stato = 0 ) {
        $send = new MasSingleSend;
        $send->id_con_mas_invio_contact = $contatto->id;
        $send->id_rubrica_contatto = $contatto->id_rubrica_contatto;
        $send->tipo_rubrica_contatto = $contatto->tipo_rubrica_contatto;
        $send->valore_rubrica_contatto = $contatto->valore_rubrica_contatto;
        $send->channel = $channel;
        $send->id_invio = $invio->id;
        $send->status = $stato;
        if(!$send->save()) throw new \Exception("Impossibile salvare il singolo invio",1);
    }
}
