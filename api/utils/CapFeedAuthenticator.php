<?php

namespace api\utils;

use Yii;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\web\UnauthorizedHttpException;

use common\models\cap\CapConsumer;

/**
 *
 * @author Fabio Rizzo
 */
class CapFeedAuthenticator extends JwtHttpBearerAuth
{
    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {

        \Yii::$app->response->headers->add('WWW-Authenticate', 'Basic realm="Cap"');
        \Yii::$app->response->statusCode = 401;
        \Yii::$app->response->send();

        exit();
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if (!$authHeader) {
            \Yii::$app->response->headers->add('WWW-Authenticate', 'Basic realm="Cap"');
            \Yii::$app->response->statusCode = 401;
            \Yii::$app->response->send();

            exit;
        }

        preg_match('/^Basic\s+(.*?)$/', $authHeader, $matches);
        if (count($matches) < 2) {
            return null;
        }

        $credentials = explode(":", base64_decode($matches[1]));
        $username = $credentials[0];
        $pwd = $credentials[1];

        $consumer = CapConsumer::findOne(['username'=>$username]);
        if (!$consumer) {
            \Yii::$app->response->headers->add('WWW-Authenticate', 'Basic realm="Cap"');
            \Yii::$app->response->statusCode = 401;
            \Yii::$app->response->send();

            exit;
        }

        if (!Yii::$app->security->validatePassword($pwd, $consumer->password_hash)) {
            return null;
        }

        Yii::$app->consumer->setIdentity($consumer);
        return $consumer;
    }
}
