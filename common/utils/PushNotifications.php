<?php 

namespace common\utils;

use Yii;
use common\models\utility\UtlContatto;
use common\utils\push\Android;
use common\utils\push\Ios;

class PushNotifications {

	public static function sendPushMessage( $message, $recipients ) {
		$android = [];
		$ios = [];

		foreach ($recipients as $recipient) {
			if($recipient->vendor == 'android') {
				$android[] = $recipient;
			} else {
				$ios[] = $recipient;
			}
		}

		$errors = null;

		if(count($ios) > 0) {
			try {
				$_ios = new Ios();
				$_ios->send( $message, $ios );
			} catch(\Exception $e) {
				Yii::error($e, 'push_attivazione');
				$errors = $e;
			}
		}

		if(count($android) > 0) {
			try {
				$_android = new Android();
				$_android->send( $message, $android );
			} catch(\Exception $e) {
				Yii::error($e, 'push_attivazione');
				$errors = $e;
			}
		}

		if($errors) throw $errors;

		return;
	}

}