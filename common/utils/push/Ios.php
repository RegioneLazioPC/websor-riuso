<?php 

namespace common\utils\push;

use Yii;
use common\models\utility\UtlContatto;
use common\models\app\AppConfig;
use common\models\UplMedia;
use Lcobucci\JWT\Signer\Key;

class Ios {

	public static $config = null;
	protected static $certificate_path = null;
	protected static $jwt_token = null;

	public static function getConfig() {
		return self::$config ? self::$config : self::buildConfig();
	}

	private static function buildConfig() {
		$has_to_send = AppConfig::findOne(['key'=>'ios_push']);
		if(!$has_to_send){
			self::$config = false;
			return self::$config;
		}

		$val = json_decode($has_to_send->value);
        
        self::$config = $val;

        $media_id = json_decode($val->certificate)->ID_FILE;
        $media = UplMedia::findOne($media_id);
        if(!$media) throw new \Exception("Certificato push non valido", 1);

        $path = Yii::getAlias('@backend/uploads/');
        $file_path = $path.$media->ext.'/'.$media->date_upload.'/'.$media->nome;
        self::$certificate_path = $file_path;

		return self::$config;
	}

	public function send($message, $recipients) {
		$config = self::getConfig();
		if(!$config) return;

		$this->buildToken();

		$payloadArray['aps'] = [
          'alert' => [
            'title' => $message['title'],
            'body' => $message['push_message'],
          ],
          'sound' => 'default',
          'badge' => 1
        ];

        $payloadJSON = json_encode($payloadArray);

        $send_to = [];
        foreach ($recipients as $recipient) {
        	$send_to[] = ($config->environment == 'sandbox') ? "https://api.sandbox.push.apple.com/3/device/".$recipient->contatto : "https://api.push.apple.com/3/device/".$recipient->contatto;
        }

        foreach ($send_to as $url) {
        	$ch = curl_init($url);

	        $jwt_token = self::$jwt_token;
	        $apns_topic = $config->topic;

	        if(!defined('CURL_HTTP_VERSION_2')) define('CURL_HTTP_VERSION_2', 3);

	        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJSON);
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $jwt_token","apns-topic: $apns_topic"]);
	        curl_setopt($ch, CURLOPT_HEADER, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        
	        $result = curl_exec($ch);
	        
	        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        	
        	if($httpcode != 200) Yii::error($result, 'push_attivazione');
        }
        

        return true;

	}

	protected function buildToken() {
		
		$p8file = self::$certificate_path;
		if(!$p8file) throw new \Exception("CERTIFICATO APPLE NON TROVATO", 1);

		self::$jwt_token = Yii::$app->jwt->getBuilder()
			->setIssuer(self::$config->team_id) 
            ->setHeader('kid', self::$config->key)
            ->setIssuedAt(time())
            ->getToken(new \Lcobucci\JWT\Signer\Ecdsa\Sha256(), new Key('file://'.$p8file));

	}

}