<?php

namespace api\modules\cap\controllers;

use Yii;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\utils\ResponseError;
use sizeg\jwt\JwtHttpBearerAuth;

use common\models\cap\CapExposedMessage;

class AtomController extends ActiveController
{
    public $modelClass = 'common\models\cap\CapExposedMessage';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['index']);
        unset($actions['update']);
        unset($actions['delete']);
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
        $pageURL .= str_replace("/atom", "/", $clean_uri_a[0]);


        $pagination = Yii::$app->params['cap']['pagination'];

        \Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        $response_content = '';
        
        $current_user_username = '';
        
        $response_content .= '<?xml version="1.0" encoding="UTF-8"?>';
        $response_content .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:app="http://www.w3.org/2005/Atom" xml:base="/cap/atom" xml:lang="it-IT">';
        
        $description = Yii::$app->params['cap']['sender'] . "/" . $current_user_username;
        $response_content .= '<id>'.Html::encode($description).'</id>';
        
        $response_content .= '<title>Feed of CAP Alerts from ' . Html::encode(Yii::$app->params['cap']['sender']).'</title>';
        $response_content .= '<author><name>'.Html::encode(Yii::$app->params['cap']['sender']).'</name></author>';
        

        
        $link = $pageURL . "atom";
        $response_content .= '<link href="'.$link.'" rel="self" />';

        $page = Yii::$app->request->get('page') ?? 1;
        $next = $page+1;


        $offset = ($page * $pagination) -$pagination;
        $limit = $pagination;
        
        $q_m = CapExposedMessage::find();

        if (!empty(Yii::$app->consumer->identity->geom)) {
            $q_m->andWhere('( ST_DWithin( (SELECT geom FROM cap_consumer WHERE id = :id_consumer), ST_Transform( ST_SetSRID( ST_MakePoint(lon, lat), 4326), 32632), 5000 ) )', [':id_consumer'=>Yii::$app->consumer->identity->id]);
        }

        $messages = $q_m->limit($limit)->offset($offset)->orderBy(['id'=>SORT_DESC])->all();

        
        $response_content .= '<language>it-IT</language>';
        
        
        $response_content .= '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="'.$pageURL . "atom?page=" . $page .'" rel="self" type="application/rss+xml" />';
        
        $response_content .= '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="'.$pageURL . "atom?page=" . $next . '" rel="next" type="application/rss+xml" />';
        if ($page > 1) {
            $prev = $page-1;
            $response_content .= '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="'.$pageURL . "atom?page=" . $prev .'" rel="previous" type="application/rss+xml" />';
        }

        foreach ($messages as $message) {
            $item_id = $pageURL . 'message/' . $message->identifier;
            $item_link = $item_id . ".cap";
            $pub = \DateTime::createFromFormat('Y-m-d H:i:sP', $message->effective.":00");
            if (!is_bool($pub)) {
                $pub->setTimezone(new \DateTimeZone('Europe/Rome'));
            }
            $exp = \DateTime::createFromFormat('Y-m-d H:i:sP', $message->expires.":00");
            if (!is_bool($exp)) {
                $exp->setTimezone(new \DateTimeZone('Europe/Rome'));
            }

            $response_content .= '<entry>';

            $response_content .= '<id>'.Html::encode($item_id).'</id>';

            $links = ['edit-media', 'enclosure', 'alternate'];
            foreach ($links as $l) {
                $response_content .= '<link href="'.$item_link.'" rel="'.$l.'" />';
            }

            $response_content .= '<published>' . $pub->format('Y-m-d\TH:i:s.000\Z') . '</published>';
            $response_content .= '<content type="application/cap+xml" src="'.$item_link.'" />';

            $response_content .= '<title>' . Html::encode($message->headline) . '</title>';
            $response_content .= '<summary>' . Html::encode($message->description) . '</summary>';
            $response_content .= '<category term="'.$message->status.'" scheme="urn:oasis:names:tc:emergency:cap:1.2:status" />';
            $response_content .= '<category term="'.$message->msgType.'" scheme="urn:oasis:names:tc:emergency:cap:1.2:msgtype" />';

            $response_content .= '<author><name>'.Html::encode($message->sender).'</name></author>';

            $response_content .= '<category term="'.$message->category.'" scheme="urn:oasis:names:tc:emergency:cap:1.2" label="'.$message->category.'" />';
            $response_content .= '<category term="'.$message->urgency.'" scheme="urn:oasis:names:tc:emergency:cap:1.2:urgency" />';
            $response_content .= '<category term="'.$message->severity.'" scheme="urn:oasis:names:tc:emergency:cap:1.2:severity" />';
            $response_content .= '<category term="'.$message->certainty.'" scheme="urn:oasis:names:tc:emergency:cap:1.2:certainty" />';
            
            $effective =

            $response_content .= '<effective xmlns="urn:oasis:names:tc:emergency:cap:1.2">' . $pub->format('Y-m-d\TH:i:s.000\Z') . '</effective>';

            $response_content .= !is_bool($exp) ? '<expires xmlns="urn:oasis:names:tc:emergency:cap:1.2">'. $exp->format('c')."Z".'</expires>' : '<expires xmlns="urn:oasis:names:tc:emergency:cap:1.2" />';

            $update = \DateTime::createFromFormat('U', $message->updated_at);
            $response_content .= '<app:edited>'.$update->format("Y-m-d\TH:i:s.000\Z").'</app:edited>';
            $response_content .= '<updated>'.$update->format("Y-m-d\TH:i:s.000\Z").'</updated>';
            

            $response_content .= '</entry>';
        }
        
        $response_content .= '</feed>';
        

        \Yii::$app->response->content = $response_content;
        \Yii::$app->response->send();
    }
}
