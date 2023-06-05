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

class MessageController extends ActiveController
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

    /**
     * Singolo messaggio cap
     *
     * @return [type] [description]
     */
    public function actionView($identifier)
    {

        date_default_timezone_set('Europe/Rome');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        
        $q_m = CapExposedMessage::find()->where(['identifier'=>$identifier]);

        if (!empty(Yii::$app->consumer->identity->geom)) {
            $q_m->andWhere('( ST_DWithin( (SELECT geom FROM cap_consumer WHERE id = :id_consumer), ST_Transform( ST_SetSRID( ST_MakePoint(lon, lat), 4326), 32632), 5000 ) )', [':id_consumer'=>Yii::$app->consumer->identity->id]);
        }

        $message = $q_m->one();
        if (empty($message)) {
            throw new \yii\web\HttpException(404, "Risorsa non trovata", 1);
        }
        

        $response_content = '';

        $response_content .= '<?xml version="1.0" encoding="UTF-8"?>';
        $response_content .= '<alert xmlns="urn:oasis:names:tc:emergency:cap:1.2">';
        $response_content .= '<identifier>'.Html::encode($message->identifier).'</identifier>';
        $response_content .= '<sender>'.Html::encode($message->sender).'</sender>';
        $response_content .= $this->getDate($message->sent, 'sent');
        $response_content .= '<status>'.Html::encode($message->status).'</status>';
        $response_content .= '<msgType>'.Html::encode($message->msgType).'</msgType>';
        $response_content .= $message->source ? '<source>'.Html::encode($message->source).'</source>' : '';
        $response_content .= $message->scope ? '<scope>'.Html::encode($message->scope).'</scope>' : '<scope />';
        $response_content .= $message->restriction ? '<restriction>'.Html::encode($message->restriction).'</restriction>' : '<restriction />';
        $response_content .= $message->addresses ? '<addresses>'.Html::encode($message->addresses).'</addresses>' : '<addresses />';
        $response_content .= $message->code ? '<code>'.Html::encode($message->code).'</code>' : '<code />';
        $response_content .= $message->note && (
            $message->status == 'Esercise' ||
            $message->msgType == 'Error'
        ) ? '<note>'.$message->note.'</note>' : '';
        $response_content .= $message->references ? '<references>'.Html::encode($message->references).'</references>' : '<references />';
        $response_content .= $message->incidents ? '<incidents>'.Html::encode($message->incidents).'</incidents>' : '<incidents />';

        $response_content .= '<info>';
        $response_content .= $message->language ? '<language>'.Html::encode($message->language).'</language>' : '<language />';
        $response_content .= $message->category ? '<category>'.Html::encode($message->category).'</category>' : '<category />';
        $response_content .= $message->event ? '<event>'.Html::encode($message->event).'</event>' : '<event />';
        $response_content .= $message->response_type ? '<responseType>'.Html::encode($message->response_type).'</responseType>' : '<responseType />';
        $response_content .= $message->urgency ? '<urgency>'.Html::encode($message->urgency).'</urgency>' : '<urgency />';
        $response_content .= $message->severity ? '<severity>'.Html::encode($message->severity).'</severity>' : '<severity />';
        $response_content .= $message->certainty ? '<certainty>'.Html::encode($message->certainty).'</certainty>' : '<certainty />';
        $response_content .= $message->audience ? '<audience>'.Html::encode($message->audience).'</audience>' : '<audience />';

        $ec = json_decode($message->eventCode, true);
        foreach ($ec as $v) {
            $response_content .= '<eventCode>';
            $response_content .= '<valueName>' . Html::encode($v['valueName']) . '</valueName>';
            $response_content .= '<value>' . Html::encode($v['value']) . '</value>';
            $response_content .= '</eventCode>';
        }


        $response_content .= $this->getDate($message->effective, 'effective');
        $response_content .= $this->getDate($message->onset, 'onset');
        $response_content .= $this->getDate($message->expires, 'expires', false);
        
        $response_content .= $message->senderName ? '<senderName>'.Html::encode($message->senderName).'</senderName>' : '<senderName />';
        $response_content .= $message->headline ? '<headline>'.Html::encode($message->headline).'</headline>' : '<headline />';
        $response_content .= $message->description ? '<description>'.Html::encode($message->description).'</description>' : '<description />';

        $response_content .= $message->instruction ? '<instruction>'.Html::encode($message->instruction).'</instruction>' : '<instruction />';
        $response_content .= $message->web ? '<web>'.$message->web.'</web>' : '<web />';
        $response_content .= $message->contact ? '<contact>'.Html::encode($message->contact).'</contact>' : '<contact />';

        $ec = json_decode($message->parameter, true);
        foreach ($ec as $v) {
            $response_content .= '<parameter>';
            $response_content .= '<valueName>' . Html::encode($v['valueName']) . '</valueName>';
            $response_content .= '<value>' . Html::encode($v['value']) . '</value>';
            $response_content .= '</parameter>';
        }

        $ec = json_decode($message->resource, true);
        foreach ($ec as $v) {
            $response_content .= '<resource>';
            foreach ($v as $key => $value) {
                $response_content .= '<'.$key.'>' . Html::encode($value) . '</'.$key.'>';
            }
            $response_content .= '</resource>';
        }

        $response_content .= '<area>';
        $area = json_decode($message->area, true);
        $response_content .= '<areaDesc>'.Html::encode($area['areaDesc']).'</areaDesc>';
        $response_content .= '<circle>'.Html::encode($area['circle']).'</circle>';
        $response_content .= '</area>';

        $response_content .= '</info>';
        
        $response_content .= '</alert>';
        

        \Yii::$app->response->content = $response_content;
        \Yii::$app->response->send();
    }

    private function getDate($dt, $tag, $write_tag = true)
    {
        

        $d_o = \DateTime::createFromFormat('Y-m-d H:i:sP', $dt."00");
        if (is_bool($d_o)) {
            if ($write_tag) {
                return '<' . $tag . ' />';
            } else {
                return '';
            }
        }
        $d_o->setTimezone(new \DateTimeZone('Europe/Rome'));
        
        return '<'.$tag.'>'.$d_o->format('c').'</'.$tag.'>';
    }
}
