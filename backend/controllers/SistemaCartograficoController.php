<?php

namespace backend\controllers;


use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * SegnalazioneController implements the CRUD actions for UtlSegnalazione model.
 */
class SistemaCartograficoController extends Controller
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
                        'actions' => ['index'],
                        'permissions' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all UtlSegnalazione models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->renderPartial('index', []);
    }

    
}
