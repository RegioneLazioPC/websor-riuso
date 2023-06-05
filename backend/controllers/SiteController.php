<?php
namespace backend\controllers;

use common\models\DbSession;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;

/**
 * Site controller
 *
 * Controller per la gestione dell'autenticazione
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'request-password-reset', 'reset-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
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
     * Mostra l'homepage
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->redirect('evento/index');
    }

    /**
     * Login action.
     *
     * Accesso utente
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //Delete session items where id_user is null and last_write is > of last 48hours
            $dataLimite = date('Y-m-d H:i:s', strtotime("-48 hour"));
            DbSession::deleteAll(['and', ['id_user' => null], ['<', 'last_write', $dataLimite]]);

            //Delete session items where id_user is = current user id
            DbSession::deleteAll(['id_user' => $model->user->id]);

            \common\models\app\AppAccessLog::addLogElement('Login', []);
            
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * Logout utente
     *
     * @return string
     */
    public function actionLogout()
    {
        \common\models\app\AppAccessLog::addLogElement('Logout', []);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset action.
     *
     * Richiesta di reset della password
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Controlla la mail per ulteriori istruzioni.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Ci dispiace, ma non è stato possibile inviare la nuova password alla mail indicata');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * Reset della password
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Nuova password salvata.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
