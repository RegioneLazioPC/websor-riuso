<?php 

namespace common\utils\push;

use Yii;
use common\models\utility\UtlContatto;
use common\models\app\AppConfig;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;
class Android {

	public static $config = null;

	public static function getConfig() {
		return self::$config ? self::$config : self::buildConfig();
	}

	private static function buildConfig() {
		$has_to_send = AppConfig::findOne(['key'=>'android_push']);
		if(!$has_to_send){
			self::$config = false;
			return self::$config;
		}

		$val = json_decode($has_to_send->value);
        if(empty($val->api_key)) return;

        self::$config = [
        	'api_key' => $val->api_key
        ];

		return self::$config;
	}
	
	public function send($message, $recipients) {
		$config = self::getConfig();
		if(!$config) return;

		$fcm_client = new Client();
        $fcm_client->setApiKey($config['api_key']);
        $fcm_client->injectHttpClient( new \GuzzleHttp\Client( [] ) );
		
		$notification = new Notification( $message['title'], $message['push_message']);
		$notification->setIcon('default_notification_icon')
            ->setColor('#ffffff')
            ->setBadge(1);

        $msg = new Message();
        $msg
        	->setNotification($notification)
        	->setData(['title' => $message['title'],'body'=>$message['push_message'] ]);

        foreach ($recipients as $contatto) {
        	$msg->addRecipient( new Device($contatto->contatto) );
        }
        
        $response = $fcm_client->send($msg);
    	return [
	    	'RESPONSE_CODE'=>$response->getStatusCode()
	    ];

	}

}