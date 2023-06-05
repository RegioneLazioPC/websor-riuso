<?php

namespace api\modules\map\controllers;

use Exception;
use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use sizeg\jwt\JwtHttpBearerAuth;
use sizeg\jwt\Jwt;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use api\utils\ResponseError;

use common\models\User;
use common\models\LoginForm;

/**
 * Auth Controller
 *
 */
class AuthController extends ActiveController
{
    public $modelClass = 'common\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] = [
                'class' => \api\utils\Authenticator::class,
                'except' => ['options', 'login','test'],
            ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options','login','test'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['profile'],
                    'roles' => ['@']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    
    public function actionOptions()
    {
        return ['message'=>'ok'];
    }

    public function actionTest()
    {
        return "ciao";
    }


    /**
     * Login
     * @return [type] [description]
     */
    public function actionLogin()
    {
        $model = new LoginForm();
                
        $request = Yii::$app->getRequest();

        if ($model->load($request->getBodyParams(), '') && $model->login()) :
            $request = new yii\web\Request;
            $ip = $request->getUserIP();
            $agent = $request->getUserAgent();

            $signer = new Sha256();

            $token = Yii::$app->jwt->getBuilder()
            ->setIssuer(Yii::$app->params['iss']) // Configures the issuer (iss claim)
            ->setAudience(Yii::$app->params['aud']) // Configures the audience (aud claim)
            ->setId(Yii::$app->params['tid'], true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            ->setNotBefore(time()) // Configures the time before which the token cannot be accepted (nbf claim)
            ->setExpiration(time() + (3600*24)) // Configures the expiration time of the token (exp claim)
            ->set('uid', Yii::$app->user->identity->id) // Configures a new claim, called "uid"
            ->set('ip', $ip)
            ->set('agent', $agent)
            ->sign($signer, Yii::$app->params['secret-key'])
            ->getToken(); // Retrieves the generated token

            $user = User::findOne(Yii::$app->user->identity->id);
            Yii::$app->user->identity = $user;
            \common\models\app\AppAccessLog::addLogElement('Login', []);
            $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->identity->id);
            return [ 'token' => "" . $token, 'user' => $user, 'permissions' => $permissions ];
        else :
            ResponseError::returnMultipleErrors(422, $model->getErrors());
        endif;
    }

    /**
     * Profilo utente con permessi
     * @return [type] [description]
     */
    public function actionProfile()
    {
        $user = User::findOne(Yii::$app->user->identity->id);
        $permissions = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->identity->id);
        return [ 'user' => $user, 'permissions' => $permissions ];
    }
}
