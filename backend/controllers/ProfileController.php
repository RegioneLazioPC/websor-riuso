<?php

namespace backend\controllers;


use common\models\User;
use common\models\UserChangePassword;

use Exception;
use Yii;
use common\models\UtlEvento;
use common\models\UtlEventoSearch;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\debug\models\timeline\DataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Profilo utente
 */
class ProfileController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className()
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
                        'actions' => ['view'],
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }
    

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
        $model = User::findOne(Yii::$app->user->identity->id);
        $ana_errors = [];
        $pwd_errors = [];
        $ok_pwd = false;

        if(Yii::$app->request->method == 'POST' && Yii::$app->request->post('UtlAnagrafica')) :

            $anagrafica = ($model->utente) ? $model->utente->anagrafica : $model->operatore->anagrafica;
            
            
            $anagrafica->load(Yii::$app->request->post());

            if(!$anagrafica->save()) $errors = $anagrafica->getErrors();
            
        endif;

        if(Yii::$app->request->method == 'POST' && Yii::$app->request->post('UserChangePassword')) :
            
            $change_pwd_model = new UserChangePassword();
            $change_pwd_model->load(Yii::$app->request->post());
            if($change_pwd_model->validate(['new_password','old_password','repeat_password'])) :
                $params = Yii::$app->request->post();
                $model->setPassword($params['UserChangePassword']['new_password']);
                $model->save();
                $ok_pwd = true;
            else:
                $pwd_errors = $change_pwd_model->getErrors();
            endif;
            
        endif;

        return $this->render('view', [
            'model' => $model,
            'ana_errors' => $ana_errors,
            'pwd_errors' => $pwd_errors,
            'ok_pwd' => $ok_pwd
        ]);
    }

    public function actionChangePwd()
    {
        // alla fine faccio il redirect
    }

    
}
