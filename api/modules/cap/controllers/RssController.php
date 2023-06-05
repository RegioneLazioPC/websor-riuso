<?php

namespace api\modules\cap\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\utils\ResponseError;
use sizeg\jwt\JwtHttpBearerAuth;

use yii\helpers\Html;
use common\models\cap\CapExposedMessage;

class RssController extends ActiveController
{
    public $modelClass = 'common\models\cap\CapExposedMessage';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['index']);
        unset($actions['update']);
        unset($actions['view']);
        unset($actions['delete']);
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors =  [
            'authenticator' => [
                'class' => \api\utils\CapFeedAuthenticator::class,
                'except' => ['options']
            ],
        ];
        return $behaviors;
    }

    public function actionOptions()
    {
        return ['message'=>'ok'];
    }

    public function actionIndex()
    {

        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }

        if (isset($_SERVER['X-Forwarded-Proto'])) {
            $pageURL = $_SERVER['X-Forwarded-Proto'];
        }

        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= isset($_SERVER["X-Forwarded-Host"]) ? $_SERVER["X-Forwarded-Host"] : $_SERVER["SERVER_NAME"];
            $pageURL .= ":" . $_SERVER["SERVER_PORT"];
        } else {
            $pageURL .= isset($_SERVER["X-Forwarded-Host"]) ? $_SERVER["X-Forwarded-Host"] : $_SERVER["SERVER_NAME"];
        }

        $clean_uri_a = explode("?", $_SERVER['REQUEST_URI']);
        $pageURL .= str_replace("/rss", "/", $clean_uri_a[0]);


        $pagination = Yii::$app->params['cap']['pagination'];
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        
        $q_m = CapExposedMessage::find()->limit($pagination)->orderBy(['id'=>SORT_DESC]);

        if (!empty(Yii::$app->consumer->identity->geom)) {
            $q_m->andWhere('( ST_DWithin( (SELECT geom FROM cap_consumer WHERE id = :id_consumer), ST_Transform( ST_SetSRID( ST_MakePoint(lon, lat), 4326), 32632), 5000 ) )', [':id_consumer'=>Yii::$app->consumer->identity->id]);
        }

        $messages = $q_m->all();
        

        $response_content = '';

        $response_content .= '<?xml version="1.0" encoding="UTF-8"?>';
        $response_content .= '<rss version="2.0">';
        $response_content .= '<channel>';
        $response_content .= '<title>Feed of CAP Alerts from ' . Html::encode(Yii::$app->params['cap']['sender']).'</title>';
        $link = $pageURL . "rss";
        $response_content .= '<link>'.Html::encode($link).'</link>';

        $current_user_username = '';
        $description = Yii::$app->params['cap']['sender'] . "/" . $current_user_username;
        $response_content .= '<description>'.Html::encode($description).'</description>';
        $response_content .= '<language>it-IT</language>';
        $response_content .= '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="'.$pageURL . "atom".'" rel="self" type="application/rss+xml" />';
        foreach ($messages as $message) {
            $response_content .= '<item>';
            $response_content .= '<title>' . Html::encode($message->headline) . '</title>';
            $response_content .= '<link>' . Html::encode($pageURL . 'message/' . $message->identifier) . '.cap</link>';
            $response_content .= '<description>' . Html::encode($message->description) . '</description>';
            $response_content .= '<category>' . Html::encode($message->category) . '</category>';

            $pub = \DateTime::createFromFormat('Y-m-d H:i:sP', $message->effective.":00");

            $response_content .= '<pubDate>' . preg_replace("/\+([0-9]+)$/", "", $pub->format('D, d M Y H:i:s T')) . '</pubDate>';
            $response_content .= '<guid>' . Html::encode($pageURL . 'message/' . $message->identifier) . '.cap</guid>';
            $response_content .= '</item>';
        }
        $response_content .= '</channel>';
        $response_content .= '</rss>';
        

        \Yii::$app->response->content = $response_content;
        \Yii::$app->response->send();
    }
}
