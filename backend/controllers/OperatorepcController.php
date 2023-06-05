<?php

namespace backend\controllers;

use common\models\User;
use Exception;
use Yii;
use common\models\UtlOperatorePc;
use common\models\UtlOperatorePcSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\UtlAnagrafica;

/**
 * OperatorePcController implements the CRUD actions for UtlOperatorePc model.
 */
class OperatorepcController extends Controller
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
                    'lockUnlock' => ['POST']
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user) {
                        Yii::error("Tentativo di accesso non autorizzato aggregatori user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['viewOperatore']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createOperatore']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'set-mobile', 'set-use-type', 'set-default', 'add-contatto-rubrica', 'delete-contatto-rubrica'],
                        'permissions' => ['updateOperatore']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'lock-unlock'],
                        'permissions' => ['deleteOperatore']
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all UtlOperatorePc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UtlOperatorePcSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Unlock operator
     * @param integer $id
     * @return mixed
     */
    public function actionLockUnlock($id)
    {
        $model = $this->findModel($id);
        
        if (!empty($model->user)) {
            $u = $model->user;
            $u->status = ($u->status == \common\models\User::STATUS_DELETED) ? \common\models\User::STATUS_ACTIVE : \common\models\User::STATUS_DELETED;
            $u->save(false);
        }


        return $this->redirect(['index']);
    }

    /**
     * Displays a single UtlOperatorePc model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UtlOperatorePc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UtlOperatorePc();
        $model->scenario = 'createOperatore';
        $anagrafica = new UtlAnagrafica();


        
        if ($anagrafica->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {
                $anagrafica = $anagrafica->createOrUpdate();
                // Create utente
                if ($anagrafica->getErrors()) {
                    throw new Exception('Errore salvataggio utente. Controllare i dati', 500);
                }

                $model->id_anagrafica = $anagrafica->id;
                // Create utente
                if (!$model->save()) {
                    throw new Exception('Errore salvataggio utente. Controllare i dati', 500);
                }
                
                //Create user
                $user = new User();
                $user->username = $model->username;//$model->matricola;
                $user->email = $anagrafica->email;
                $user->setPassword($model->password/*$model->matricola.substr($model->cognome, 0, 3)*/);
                
                $user->generateAuthKey();
                if (!$user->validate()) :
                    throw new Exception('Dati utente non validi, probabilmente lo username o la mail già sono nel sistema', 422);
                endif;
                
                if (!$user->save()) {
                    throw new Exception('Errore salvataggio utente. Controllare i dati', 500);
                }
                
                // Add Permission
                $auth = Yii::$app->authManager;
                $authorRole = $auth->getRole($model->ruolo);
                if (!$authorRole) {
                    throw new Exception('Ruolo non valido', 500);
                }
                $auth->assign($authorRole, $user->getId());

                // Save iduser in operatore
                $model->iduser = $user->getId();
                //$model->username = $model->matricola;
                //$model->password = $model->matricola.substr($model->cognome, 0, 3);

                $model->save(false);

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                //echo $e->getMessage();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                ]);
                //exit;
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UtlOperatorePc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'updateOperatore';
        $anagrafica = $model->anagrafica;
        $old_role = $model->ruolo;

        // Mostra il form nel caso non ci siano dati da aggiornare
        if (!Yii::$app->request->post()) {
            return $this->render('update', ['model' => $model]);
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if (!$anagrafica->load(Yii::$app->request->post())) {
                throw new Exception('Si è verificato un problema sui dati anagrafici', 101);
            }
            if (!$anagrafica->save()) {
                throw new Exception('Si è verificato un problema sul salvataggio dati anagrafici', 101);
            }

            if (!$model->load(Yii::$app->request->post())) {
                throw new Exception('Si è verificato un problema sui dati', 101);
            }
            if (!$model->save()) {
                throw new Exception('Si è verificato un problema sul salvataggio dei dati', 102);
            }

            
            if ($old_role != $model->ruolo) :
                // Aggiorna i permessi
                $auth = Yii::$app->authManager;

                $oldAuthRole = $auth->getRole($old_role);
                $auth->revoke($oldAuthRole, $model->iduser);
                
                
                $authorRole = $auth->getRole($model->ruolo);
                $auth->assign($authorRole, $model->iduser);
            endif;

            // Salvo user data
            $mdUser = $model->user;
            if ($mdUser) {
                if ($mdUser->email != $anagrafica->email) {
                    $mdUser->email = $anagrafica->email;
                }

                if ($mdUser->username != $model->username) {
                    $mdUser->username = $model->username;
                }

                if (!empty($model->password)) {
                    $mdUser->setPassword($model->password);
                }

                if (!$mdUser->save()) {
                    Yii::error($mdUser->getErrors());
                    throw new Exception('Errore modifica email user. Controllare i dati', 500);
                }
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->render('update', [
              'model' => $model,
            ]);
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing UtlOperatorePc model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);
        $model->removeFromEverbridge();
        $model->delete();

        return $this->redirect(['index']);
    }



    /**
     * Aggiunge un contatto ad un elemento di mas_rubrica
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionAddContattoRubrica($id)
    {

        $base_model = $this->findModel($id);
        
        $contatto = new \common\models\utility\UtlContatto;
        $contatto->load(Yii::$app->request->post());
        if ($contatto->save()) :
            $base_model->link('contatto', $contatto, ['use_type'=>$contatto->use_type, 'type'=>$contatto->type]);
        endif;

        $base_model->syncEverbridge();

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Elimina un contatto di un elemento di rubrica
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id      [description]
     * @param  [type] $contatto_type    [description]
     * @return [type]                   [description]
     */
    public function actionDeleteContattoRubrica(
        $id
    ) {
        $c = \common\models\operatore\ConOperatorePcContatto::find()->where(['id'=>$id])->one();
        if ($c) {
            $c->delete();
        }

        $base_model = $this->findModel($c->id_operatore_pc);
        $base_model->syncEverbridge();
        
        return $this->redirect(['view', 'id'=>$c->id_operatore_pc]);
    }


    /**
     * Imposta contatto predefinito o no
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id      [description]
     * @param  [type] $contatto_type    [description]
     * @param  [type] $value            [description]
     * @return [type]                   [description]
     */
    public function actionSetDefault(
        $id
    ) {
        $c = \common\models\operatore\ConOperatorePcContatto::find()->where(['id'=>$id])->one();
        if (!$c) {
            throw new NotFoundHttpException("Contatto non trovato");
        }
        
        // cancella solo i contatti
        $c->contatto->check_predefinito = ($c->contatto->check_predefinito == 1) ? 0 : 1;
        if (!$c->contatto->save()) {
            Yii::error(json_encode($c->contatto->getErrors()));
            throw new \Exception("Errore impostazione predefinito", 1);
        }

        return $this->redirect(['view', 'id'=>$c->id_operatore_pc]);
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
        $id
    ) {
        $c = \common\models\operatore\ConOperatorePcContatto::find()->where(['id'=>$id])->one();
        if (!$c) {
            throw new NotFoundHttpException("Contatto non trovato");
        }
        
        
        $c->contatto->check_mobile = ($c->contatto->check_mobile == 1) ? 0 : 1;
        if (!$c->contatto->save()) {
            Yii::error(json_encode($c->contatto->getErrors()));
            throw new \Exception("Errore impostazione predefinito", 1);
        }

        return $this->redirect(['view', 'id'=>$c->id_operatore_pc]);
    }

    /**
     * Imposta contatto messaggistica o allertamento
     * @param  [type] $id_riferimento   [description]
     * @param  [type] $tipo_riferimento [description]
     * @param  [type] $id      [description]
     * @param  [type] $contatto_type    [description]
     * @param  [type] $value            [description]
     * @return [type]                   [description]
     */
    public function actionSetUseType(
        $id
    ) {

        $c = \common\models\operatore\ConOperatorePcContatto::find()->where(['id'=>$id])->one();
        if (!$c) {
            throw new NotFoundHttpException("Contatto non trovato");
        }
        
        $new = ($c->use_type == 2) ? 0 : 2;
        
        $c->use_type = $new;
        if (!$c->save()) {
            Yii::error(json_encode($c->getErrors()));
            throw new \Exception("Errore impostazione predefinito", 1);
        }
        
        $c->contatto->use_type = $new;
        if (!$c->contatto->save()) {
            Yii::error(json_encode($c->contatto->getErrors()));
            throw new \Exception("Errore impostazione predefinito", 1);
        }

        $base_model = $this->findModel($c->id_operatore_pc);
        $base_model->syncEverbridge();

        return $this->redirect(['view', 'id'=>$c->id_operatore_pc]);
    }



    /**
     * Finds the UtlOperatorePc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UtlOperatorePc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UtlOperatorePc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
