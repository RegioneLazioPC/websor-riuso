<?php 
namespace common\utils;

use Yii;

/**
 * Gestione proxy like dei servizi esposti dal modulo MAS
 */
class MasHttpServices {

	public static $http = null;
	public static $instance = null;
	public static $guzzle_options = [];

	public function __construct() {
		if(!self::$http) self::$http = new \GuzzleHttp\Client( self::$guzzle_options );
	}

	public static function __callStatic ( $f, $a ) {
		if(!self::$instance) self::$instance = new static;

		
		return call_user_func_array([self::$instance, $f], $a);
	}

	
	/**
	 * Ritorna lo stato dell'invio
	 * @param  integer $id_invio 
	 */
	private function getInvioStatus( $id_invio ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'message/working/' . $id_invio;
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Ritorna i messaggi associati ad un invio
	 * @param  integer $id_invio 
	 */
	private function getInvioMessages( $id_invio ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'invio/' . $id_invio . '/messages';

			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Stoppa l'uso della coda per un invio
	 * @param  integer $id_invio 
	 */
	private function stopInvio( $id_invio ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/stop/' . $id_invio;
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Fa ripartire i messaggi dell'invio
	 * @param  integer $id_invio 
	 */
	private function restartInvio( $id_invio ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/start/' . $id_invio;
			$response = self::$http->request('POST', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Stoppa l'uso della coda per un messaggio
	 * @param  integer $id_invio 
	 */
	private function stopMessage( $id_message ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/message/stop/' . $id_message;
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Stoppa l'uso della coda per un messaggio
	 * @param  integer $id_invio 
	 */
	private function restartMessage( $id_message ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/message/start/' . $id_message;
			$response = self::$http->request('POST', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Processa manualmente un messaggio
	 * @param  integer $id_message 
	 */
	private function processManually( $id_message ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'message/' . $id_message . '/process';
			$response = self::$http->request('POST', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Riverifica un messaggio
	 * @param  integer $id_invio 
	 */
	private function reverifyMessage( $id_message ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'message/' . $id_message . '/reverify';
			$response = self::$http->request('POST', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Stoppa processo
	 * @param  integer $id_message 
	 */
	private function stopProcess( $id_message ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/message/stop/' . $id_message;
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Restart di processo
	 * @param  integer $id_message 
	 */
	private function restartProcess( $id_message ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/message/start/' . $id_message;
			$response = self::$http->request('POST', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Ritorna i contatti
	 * @param  integer $id_invio 
	 */
	private function getContacts( $id_invio ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'invio/' . $id_invio . '/contacts';
			
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return  $response->getBody()->getContents() ;

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Ritorna i log dell'invio
	 * @param  integer $id_invio 
	 */
	private function getLogs( $id_invio, $channel = 'Email' ) {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'message/' . $id_invio . '/logs/' . $channel;
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);



			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Ritorna se i consumer stanno girando
	 */
	private function isRunning() {
		try {
			
			$endpoint = Yii::$app->params['mas_host'] . 'process/verify-consumers';
			$response = self::$http->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);

			return $response->getBody()->getContents();

		} catch( \Exception $e ) {
			Yii::error($e->getMessage());
			throw $e;
		}
	}


}