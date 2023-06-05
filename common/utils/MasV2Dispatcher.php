<?php 

namespace common\utils;

use Yii;
use common\models\MasMessageTemplate;
use common\models\MasMessage;
use common\models\ConMasInvioContact;
use common\models\MasV2Feedback;

class MasV2Dispatcher {

	private static $token = null;

	protected static function getJwtToken() {
		return self::$token ?? self::buildToken();
	}

	protected static function buildToken() {

		$token = Yii::$app->jwt->getBuilder()
			->setIssuer(Yii::$app->params['iss']) 
            ->setAudience(Yii::$app->params['aud']) 
            ->setId(Yii::$app->params['tid'], true)
            ->setIssuedAt(time()-30) 
            ->setNotBefore(time()-30);

        $token->set('stringa_assegnazione', 'CONSUMER|||'.Yii::$app->user->identity->username);
        $token->set('role', Yii::$app->params['mas_consumer_role']);
        $token->set('username', Yii::$app->user->identity->username);

		// mettiamo 1 ora
		$token->setExpiration(time() + (3600));
		$signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
		$token = $token->sign($signer, Yii::$app->params['secret-key'])->getToken();

		self::$token = $token;
		return self::$token;
	}

	/**
	 * Crea messaggio con relativi allegati
	 * @param  [type] $message [description]
	 * @return [type]          [description]
	 */
	public static function createMessage( $message, $invio ) {
		$guzzle_options = [];
		$client = new \GuzzleHttp\Client( $guzzle_options );

		$template = null;
		if( !empty( $message->id_template ) ) $template = MasMessageTemplate::findOne( $message->id_template );
		$data = [
			[
				'name' => 'title',
				'contents' => $message->title
			],
			[
				'name' => 'message_type',
				'contents' => Yii::$app->params['mapping_tipo_messaggio'][ (!empty($message->id_allerta) ? 'allerta' : 'messaggio') ]
			],
			[
				// riferimento del messaggio
				'name' => 'ref',
				'contents' => 'websor_' . $invio->id
			]
		];

		$channels = [];

		if($message->channel_sms == 1) {
			$data[] = ['name'=>'sms_message', 'contents' => MasMessageManager::getPreview( $message, $template, self::getChannelIndex( 'Sms' ) ) ];
			$channels[] = 'sms';
		}

		if($message->channel_mail == 1 || $message->channel_pec == 1) {
			$data[] = ['name'=>'email_message', 'contents' => MasMessageManager::getPreview( $message, $template, self::getChannelIndex( 'Email' ) ) ];
			if($message->channel_pec == 1) $channels[] = 'pec';
			if($message->channel_mail == 1) $channels[] = 'email';
		}
		if($message->channel_fax == 1) {
			$data[] = ['name'=>'fax_message', 'contents' => MasMessageManager::getPreview( $message, $template, self::getChannelIndex( 'Fax' ) ) ];
			$channels[] = 'fax';
		}
		if($message->channel_push == 1) {
			$data[] = ['name'=>'push_message', 'contents' => MasMessageManager::getPreview( $message, $template, self::getChannelIndex( 'Push' ) ) ];
			$channels[] = 'push android';
			$channels[] = 'push ios';
		}

		if(!empty($message->allerta)) $data[] = ['name'=>'zone_allerta', 'contents' => $message->allerta->zone_allerta ];

		$data[] = ['name'=>'channels', 'contents'=>json_encode($channels)];


		foreach ($message->file as $file) {
			$path = Yii::getAlias('@backend') . '/uploads/' . $file->ext . '/' . $file->date_upload . '/' . $file->nome;
			$data[] = [
				'name' => 'file[]',
				'filename' => $file['nome'].'.'.$file['ext'],
				'contents' => fopen( $path, 'r' )
			];
		}

		if(!empty($message->allerta)) {
			foreach ($message->allerta->file as $file) {
				$path = Yii::getAlias('@backend') . '/uploads/' . $file->ext . '/' . $file->date_upload . '/' . $file->nome;
				$data[] = [
					'name' => 'file[]',
					'filename' => $file['nome'].'.'.$file['ext'],
					'contents' => fopen( $path, 'r' )
				];
			}
		}

		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	],
        	'multipart' => $data,
        ]);


		if($response->getStatusCode() != 200) {
			
			throw new \Exception($response->getBody()->getContents(), 1);			    	    	
	    	
	    } else {
	    	
	    	$body = $response->getBody()->getContents();
	    	
	    	Yii::error(json_decode($body, true));
	    	
	    	return json_decode($body, true)['data']['id'];

	    }
	}

	/**
	 * Aggiungi destinatari a un messaggio, prende come parametro la query generata
	 * @param [type] $select_query [description]
	 * @param [type] $contacts     [description]
	 */
	public static function addRecipients( $active_query, $mas_invio ) {

		$tipo_messaggio = Yii::$app->params['mapping_tipo_messaggio'][ !empty($mas_invio->message->id_allerta) ? 'allerta' : 'messaggio' ];
		
		$active_query->select(
			[
				'concat(\'websor_\', identificativo) as uid_contatto',
				'tipologia_riferimento as tipo_contatto',
				'valore_riferimento as contatto',
				'valore_contatto as recapito',
				'num_elenco_territoriale',
				'cf',
				'CASE
	              WHEN (tipo_contatto = 0) THEN \'email\'
	              WHEN (tipo_contatto = 1) THEN \'pec\'
	              WHEN (tipo_contatto in (2,4) AND check_mobile = 1) THEN \'sms\'
	              WHEN (tipo_contatto in (3,5) ) THEN \'fax\'
	              WHEN (tipo_contatto = 6 AND vendor = \'android\') THEN \'push android\'
	              WHEN (tipo_contatto = 6 AND vendor = \'ios\') THEN \'push ios\'
	            END AS channel',
	            'ext_id',
				'everbridge_identifier',
				'indirizzo',
				'comune',
				'provincia',
				'lat',
				'lon',
				'zone_allerta',
				new \yii\db\Expression('\''. $tipo_messaggio . '\' as target')
		]);
		
		$result = $active_query->asArray()->all();

		$temp = tmpfile();
		fwrite($temp, json_encode($result));

		$files = [];
		$files[] = [
			'name' => 'destinatari[]',
			'mime' => 'application/json',
			'ext' => 'json',
			'filename' => 'destinatari.json',
			'contents' => fopen( stream_get_meta_data($temp)['uri'] , 'r' )
		];
		$files[] = [
        			'name' => 'add_to_rubrica',
        			'contents' => 'true'
        		];
		

		$guzzle_options = [];
		$client = new \GuzzleHttp\Client( $guzzle_options );
		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message/'.$mas_invio->mas_ref_id.'/recipients';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	],
        	'multipart' => $files
        ]);

        fclose($temp); 


		if($response->getStatusCode() != 200) {
			
			throw new \Exception($response->getBody()->getContents(), 1);			    	    	
	    	
	    } else {
	    	
	    	$body = $response->getBody()->getContents();
	    	
	    	Yii::error(json_decode($body, true));
	    	
	    	return ['message'=>'ok'];

	    }

	}

	/**
	 * Chiedi al mas di inviare il messaggio
	 * @param  [type] $mas_invio [description]
	 * @return [type]            [description]
	 */
	public static function sendMessage( $mas_invio ) {

		$guzzle_options = [];
		$client = new \GuzzleHttp\Client( $guzzle_options );
		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message/'.$mas_invio->mas_ref_id.'/dispatch';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	]
        ]);


		if($response->getStatusCode() != 200) {
			
			throw new \Exception($response->getBody()->getContents(), 1);			    	    	
	    	
	    } else {
	    	
	    	$body = $response->getBody()->getContents();
	    	
	    	Yii::error(json_decode($body, true));
	    	// attempt id
	    	// usiamo l'attempt id da associare al mas_invio per gestire gli update
	    	return ['message'=>'ok'];

	    }
	}

	/**
	 * Reinvia messaggio a tutti
	 * @param  [type] $mas_invio [description]
	 * @param  $action [all, not_sent, selected]
	 * @return [type]            [description]
	 */
	public static function resend($mas_invio, $action, $ids = []) {

		$form_data = [[
			'name' => 'action',
			'contents' => $action
		]];

		if(count($ids) > 0) {

			$search = ConMasInvioContact::find()->where([
				'id' => $ids
			])
			->andWhere(['id_invio'=>$mas_invio->id])
			->joinWith('contatto as vw_rubrica')
			->select(new \yii\db\Expression('
				distinct concat(\'websor_\', vw_rubrica.identificativo) as uid, 
				con_mas_invio_contact.valore_rubrica_contatto as recapito,
				CASE
	              WHEN (channel = \'Email\') THEN \'email\'
	              WHEN (channel = \'Pec\') THEN \'pec\'
	              WHEN (channel = \'Sms\') THEN \'sms\'
	              WHEN (channel = \'Fax\') THEN \'fax\'
	              WHEN (channel = \'Push\' AND con_mas_invio_contact.vendor = \'android\') THEN \'push android\'
	              WHEN (channel = \'Push\' AND con_mas_invio_contact.vendor = \'ios\') THEN \'push ios\'
	            END AS _channel
			') )->all();


			//Yii::error($q->createCommand()->getRawSql());
			//$search = $q->all();
			Yii::error(json_encode((array)$search));
			/**
			 * Creo json con i contatti
			 */
			$temp = tmpfile();
			fwrite($temp, json_encode((array)$search));

			$form_data[] = [
				'name' => 'destinatari[]',
				'mime' => 'application/json',
				'ext' => 'json',
				'filename' => 'destinatari.json',
				'contents' => fopen( stream_get_meta_data($temp)['uri'] , 'r' )
			];
			
			fclose($temp);
			
		}


		$guzzle_options = [];
		$client = new \GuzzleHttp\Client( $guzzle_options );
		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message/'.$mas_invio->mas_ref_id.'/resend';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	],
        	'multipart' =>$form_data
        ]);


		if($response->getStatusCode() != 200) {
			
			throw new \Exception($response->getBody()->getContents(), 1);			    	    	
	    	
	    } else {
	    	
	    	$body = $response->getBody()->getContents();
	    	
	    	Yii::error(json_decode($body, true));
	    	
	    	return ['message'=>'ok'];

	    }
	}

	/**
	 * Feedback messaggi
	 *
	 * aggiorna mas_single_send
	 * @return [type] [description]
	 */
	public static function updateMessageFeedback($mas_invio, $reset_old = 0) {

		try {
			$guzzle_options = [];
			$client = new \GuzzleHttp\Client( $guzzle_options );
			$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message/'.$mas_invio->mas_ref_id.'/feedback';

			$response = $client->request('GET', $endpoint, 
	        [
	        	'headers'=>[
	        		'Authorization' => 'Bearer '.self::getJwtToken(),
	        	]
	        ]);


	        if($response->getStatusCode() != 200) {
				
				return;		    	    	
		    	
		    } else {
		    	
		    	$body = $response->getBody()->getContents();
		    	
		    	
		    	$records = json_decode($body, true);
		    	$row_to_update = [];
		    	$added = [];
		    	foreach ($records['data'] as $record) {
		    		if(isset($added[$record['unique_identifier']])) continue;
		    		
		    		$added[$record['unique_identifier']] = true;
		    		$rows[] = [
		    			$record['unique_identifier'],
		    			MasV2Feedback::channelMapped($record['channel']),
	                    $mas_invio->id,
	                    $record['recapito'],
	                    $record['driver_feedback'],
	                    $record['driver_feedback_string'],
	                    $record['driver_feedback_sent_date'],
	                    $record['driver_feedback_received_date'],
	                    $record['driver_feedback_refusing_date']
	                ]; 
		    	}

		    	$insert_command = Yii::$app->db->createCommand()
	            ->batchInsert(
	                MasV2Feedback::tableName(), 
	                [
	                    'uid',
						'channel',
						'id_invio',
						'recapito',
						'status',
						'status_string',
						'sent_date',
						'received_date',
						'refused_date',
	                ], 
	                $rows
	            );

	            $trans = Yii::$app->db->beginTransaction();

	            try {

	            	// disabilito la funzione per la criticità dell'elemento mas
	            	//if($reset_old == 1) {
	            		//Yii::$app->db->createCommand("DELETE FROM mas_v2_feedback WHERE id_invio = :id_invio", [':id_invio'=>$mas_invio->id])->execute();
		            //}
			    	
			    	$sql = $insert_command->getRawSql();
		                    $command = Yii::$app->db->createCommand( $sql . " 
		                    ON CONFLICT (uid) 
		                    DO
		                     UPDATE
		                       SET 
		                       status = EXCLUDED.status,
		                       status_string = EXCLUDED.status_string,
		                       sent_date = EXCLUDED.sent_date,
		                       received_date = EXCLUDED.received_date,
		                       refused_date = EXCLUDED.refused_date
		                    ")->execute();
		        	
		            $trans->commit();

		        } catch(\Exception $e) {
		        	Yii::error($e);
		        	$trans->rollback();
		        }

		    	return ['message'=>'ok'];

		    }

		} catch(\Exception $e) {
			Yii::error($e);
			return;
		}
	}

	/**
	 * Indice per costruzione template
	 * @param  [type] $channel [description]
	 * @return [type]          [description]
	 */
	protected static function getChannelIndex( $channel ) {
		switch( $channel ){
			case 'Email': return 0; break;
			case 'Pec': return 1; break;
			case 'Fax': return 2; break;
			case 'Sms': return 3; break;
			case 'Push': return 4; break;
		}
	}

	/**
	 * Invia richiesta al MAS in step singolo
	 * @param  [type] $data       [description]
	 * @param  [type] $recipients [description]
	 * @param  array  $files      [description]
	 * @return [type]             [description]
	 */
	public static function sendPlainMessage($data, $recipients, $files = []) {
		
		$guzzle_options = [];
		$client = new \GuzzleHttp\Client( $guzzle_options );

		$form_data = []; 
		foreach ($data as $key => $value) {
			$form_data[] = [
				'name' => $key,
				'contents' => $value
			];
		}

		foreach ($files as $file) {
			$form_data[] = [
				'name' => 'file[]',
				'filename' => $file['nome'].'.'.$file['ext'],
				'contents' => fopen( $file['path'], 'r' )
			];
		}

		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	],
        	'multipart' => $form_data,
        ]);

        if($response->getStatusCode() != 200) throw new \Exception($response->getBody()->getContents(), 1);	
			
		$body = $response->getBody()->getContents();
	    $id_mas_message = json_decode($body, true)['data']['id'];


	    $temp = tmpfile();
		fwrite($temp, json_encode($recipients));

		$files = [];
		$files[] = [
			'name' => 'destinatari[]',
			'mime' => 'application/json',
			'ext' => 'json',
			'filename' => 'destinatari.json',
			'contents' => fopen( stream_get_meta_data($temp)['uri'] , 'r' )
		];
		$files[] = [
        			'name' => 'add_to_rubrica',
        			'contents' => 'false'
        		];

        
		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message/'.$id_mas_message.'/recipients';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	],
        	'multipart' => $files
        ]);

        fclose($temp); 

		if($response->getStatusCode() != 200) throw new \Exception($response->getBody()->getContents(), 1);	


		// destinatari aggiunti
		$endpoint = Yii::$app->params['mas_v2_host'] . 'v1/message/'.$id_mas_message.'/dispatch';
		$response = $client->request('POST', $endpoint, 
        [
        	'headers'=>[
        		'Authorization' => 'Bearer '.self::getJwtToken(),
        	]
        ]);


		if($response->getStatusCode() != 200) throw new \Exception($response->getBody()->getContents(), 1);		

		return ['id'=>$id_mas_message];
	}

}