<?php

namespace backend\controllers;

use common\models\ConOperatoreTask;
use Yii;
use common\utils\EverbridgeUtility;


use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\helpers\ArrayHelper;

class EverbridgeController extends Controller
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
                        'actions' => ['paths', 'force-sync'],
                        'permissions' => ['ManageEverbridge']
                    ]
                ],

            ],
        ];
    }

    /**
     * Lista dei delivery path dell'elemento
     * @return mixed
     */
    public function actionPaths( $ext_ids )
    {
        $ext_ids = explode(",", $ext_ids);

        $everbridge_data = EverbridgeUtility::getInfoFromEverbridge( $ext_ids );

        return $this->renderPartial('paths', [
            'everbridge_data' => $everbridge_data
        ]);
    }

    /**
     * Forza la sincronizzazione con Everbridge
     * @param  [type] $model [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function actionForceSync( $model, $id ) {
        
        $element = new $model;
        $element = $element->find()->where(['id'=>$id])->one();
        
        try {
            if($element) $element->syncEverbridge();
            return "Sincronizzato";
        } catch(\Exception $e) {
            return $e->getMessage();
        }

        return "Elemento non trovato";
        
    }

}
