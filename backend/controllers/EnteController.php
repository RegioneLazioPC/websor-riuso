<?php

namespace backend\controllers;

use common\models\ConOperatoreTask;
use Yii;
use common\models\ente\EntEnte;
use common\models\ente\EntEnteSearch;
use common\models\ente\EntTipoEnte;


use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\helpers\ArrayHelper;

class EnteController extends Controller
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
                    if (Yii::$app->user) {
                        Yii::error(json_encode(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId())));
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update',
                        'tipo-ente', 'view-tipo-ente', 'update-tipo-ente'],
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

   
    public function actionIndex()
    {
        $searchModel = new EntEnteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTipoEnte()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EntTipoEnte::find(),
            'pagination' => false
        ]);

        return $this->render('tipo_ente', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionViewTipoEnte($id)
    {
        $model = $this->findTipoModel($id);

        return $this->render('view-tipo-ente', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTipoEnte($id)
    {
        $model = $this->findTipoModel($id);

        if (Yii::$app->request->method == 'POST') :
            $model->scenario = EntEnte::SCENARIO_UPDATE;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view-tipo-ente', 'id' => $model->id]);
            }
        endif;

        return $this->render('update-tipo-ente', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->method == 'POST') :
            $model->scenario = EntEnte::SCENARIO_UPDATE;
            
            $datas = Yii::$app->request->post();

            if ($datas['EntEnte']['manual_zona_update'] && $datas['EntEnte']['manual_zona_update'] == 1) {
                $datas['EntEnte']['update_zona_allerta_strategy'] = \common\models\ZonaAllertaStrategy::getZonaManuale();
                $datas['EntEnte']['zone_allerta'] = implode(",", $datas['EntEnte']['zone_allerta_array']);
            } else {
                $datas['EntEnte']['update_zona_allerta_strategy'] = $model->tipoEnte->update_zona_allerta_strategy;
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
        if (($model = EntTipoEnte::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Tipo di ente inesistente.');
    }

    protected function findModel($id)
    {
        if (($model = EntEnte::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Ente inesistente.');
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
     * @param  [type] $id   [description]
     * @return [type]                   [description]
     */
    public function actionDeleteContattoRubrica($id)
    {


        $c = \common\models\ente\ConEnteContatto::find()->where(['id'=>$id])->one();
        if ($c) {
            $id_odv = $c->id_ente;
            $c->delete();

            $base_model = $this->findModel($id_odv);
            $base_model->syncEverbridge();
        } else {
            throw new \yii\web\HttpException(404, "Recapito non trovato");
        }
        
        return $this->redirect(['view', 'id'=>$base_model->id]);
    }
}
