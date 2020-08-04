<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class LogsController extends Controller
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
                        Yii::error("Tentativo di accesso non autorizzato logs user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['segnalazioni'],
                        'permissions' => ['viewLogSegnalazioni']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['eventi'],
                        'permissions' => ['viewLogEventi']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['attivazioni'],
                        'permissions' => ['viewLogIngaggi']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['richieste-dos'],
                        'permissions' => ['viewLogRichiesteDos']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['richieste-elicottero'],
                        'permissions' => ['viewLogRichiesteElicottero']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['richieste-canadair'],
                        'permissions' => ['viewLogRichiesteCanadair']
                    ],
                ]    
            ]    
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 
     * @return string
     */
    public function actionSegnalazioni()
    {
        
        return $this->render('segnalazioni');
    }

    /**
     * 
     * @return string
     */
    public function actionEventi()
    {
        
        return $this->render('eventi');
    }

    /**
     * 
     * @return string
     */
    public function actionAttivazioni()
    {
        
        return $this->render('attivazioni');
    }

    /**
     * 
     * @return string
     */
    public function actionRichiesteDos()
    {
        
        return $this->render('dos');
    }

    /**
     * 
     * @return string
     */
    public function actionRichiesteElicottero()
    {
        
        return $this->render('elicottero');
    }

    /**
     * 
     * @return string
     */
    public function actionRichiesteCanadair()
    {
        
        return $this->render('canadair');
    }

}
