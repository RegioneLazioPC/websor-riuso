<?php

namespace backend\controllers;

use common\models\ConOperatoreTask;
use Yii;
use common\models\struttura\StrStruttura;
use common\models\struttura\StrStrutturaSearch;
use common\models\struttura\StrTipoStruttura;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\helpers\ArrayHelper;


class StrutturaController extends Controller
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
                        'actions' => ['index', 'view', 'update',
                        'tipo-struttura', 'view-tipo-struttura', 'update-tipo-struttura'],
                        'permissions' => ['Admin']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-contatto-rubrica', 'delete-contatto-rubrica'],
                        'permissions' => ['manageRecapitiOrgs']
                    ],
                ],

            ],
        ];
    }

    /**
     * Lists all StrStruttura models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StrStrutturaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTipoStruttura()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StrTipoStruttura::find(),
            'pagination' => false
        ]);

        return $this->render('tipo_struttura', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewTipoStruttura( $id )
    {
        $model = $this->findTipoModel( $id);

        return $this->render('view-tipo-struttura', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTipoStruttura( $id )
    {
        $model = $this->findTipoModel( $id);

        if(Yii::$app->request->method == 'POST') :

            $model->scenario = StrStruttura::SCENARIO_UPDATE;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view-tipo-struttura', 'id' => $model->id]);
            }

        endif;

        return $this->render('update-tipo-struttura', [
            'model' => $model,
        ]);
    }


    /**
     * Displays a single StrStruttura model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate( $id )
    {
        $model = $this->findModel( $id);

        if(Yii::$app->request->method == 'POST') :

            $model->scenario = StrStruttura::SCENARIO_UPDATE;
            
            $datas = Yii::$app->request->post();   

            if($datas['StrStruttura']['manual_zona_update'] && $datas['StrStruttura']['manual_zona_update'] == 1) {
                $datas['StrStruttura']['update_zona_allerta_strategy'] = \common\models\ZonaAllertaStrategy::getZonaManuale();
                $datas['StrStruttura']['zone_allerta'] = implode(",",$datas['StrStruttura']['zone_allerta_array']);
            } else {
                $datas['StrStruttura']['update_zona_allerta_strategy'] = $model->tipoStruttura->update_zona_allerta_strategy;
            }

            if ($model->load($datas) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        
        endif;

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findTipoModel($id)
    {
        if (($model = StrTipoStruttura::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel($id)
    {
        if (($model = StrStruttura::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Aggiunge un contatto ad un elemento di mas_rubrica
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionAddContattoRubrica($id){

        $base_model = $this->findModel($id);
        
        $contatto = new \common\models\utility\UtlContatto;
        $contatto->load(Yii::$app->request->post());
        if($contatto->save()):
            $base_model->link('contatto', $contatto, ['use_type'=>$contatto->use_type, 'type'=>$contatto->type]);
        endif;

        $base_model->syncEverbridge();

        return $this->redirect(['view','id'=>$id]);

    }

    /**
     * Elimina un contatto di un elemento di rubrica
     * @param  [type] $id   [description]
     * @return [type]                   [description]
     */
    public function actionDeleteContattoRubrica( $id ){


        $c = \common\models\struttura\ConStrutturaContatto::find()->where(['id'=>$id])->one();
        if($c) {
            
            $id_odv = $c->id_struttura;
            $c->delete();

            $base_model = $this->findModel($id_odv);
            $base_model->syncEverbridge();
        } else {
            throw new \yii\web\HttpException(404, "Recapito non trovato");
            
        }
        
        return $this->redirect(['view', 'id'=>$base_model->id]);
    }

}
