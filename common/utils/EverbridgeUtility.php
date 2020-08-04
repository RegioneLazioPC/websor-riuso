<?php 

namespace common\utils;

use Yii;

class EverbridgeUtility {

	public static function getConstants ( ) {
		return Yii::$app->params['everbridge']['const_configuration'];
	}

	public static function getGuzzleOptions() {
		return (isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) ?
			['proxy' => Yii::$app->params['proxyUrl'] ]
			: [];
	}

	/**
	 * Prende in input i record di un destinatario
	 * prende tutti i recapiti relativi
	 * inserisce ogni recapito in un array legato al record finchè non vengono esauriti gli slot 
	 * forniti da Everbridge
	 * se finiscono gli slot duplica il record e continua col parsing
	 * @param  [type] $record [description]
	 * @return [array]        Ritorna un array con uno o più record corrispondenti a questo destinatario
	 */
	public static function formatDestinatario ( $records ) {
		$const = self::getConstants();
		// per gestire i mas rubrica
		$base_ext_id = str_replace(" ", "_", $records[0]['identificativo']);
		$base_unformatted_ext_id = $records[0]['identificativo'];

		$record_recapiti = [];

		$base_record = [
            'firstName' => mb_substr( $records[0]['valore_riferimento'], 0, 30),
            'lastName' => " - ",
            'externalId' => $base_ext_id, 
            'country' => 'IT',
            'recordTypeId' => $const['recordTypeId'],
            'paths' => []
        ];

		$email_a = [];
		$email_m = [];
		$sms_a = [];
		$sms_m = [];
		$fax_a = [];
		$fax_m = [];


		$max_email_a = count($const['paths']['email']['allerta']);
		$max_email_m = count($const['paths']['email']['messaggistica']);
		$max_sms_a = count($const['paths']['sms']['allerta']);
		$max_sms_m = count($const['paths']['sms']['messaggistica']);
		$max_fax_a = count($const['paths']['fax']['allerta']);
		$max_fax_m = count($const['paths']['fax']['messaggistica']);

		foreach ( $records as $recapito ) {

			switch($recapito['tipo_contatto']) {
                case 0:
                    switch( $recapito['use_type'] ){
                        case 0: // messaggistica
                            $email_m[] = [$recapito['valore_contatto'], $recapito['id_contatto'] . "_" . $recapito['contatto_type']];
                        break;
                        case 2: // allerta
                            $email_a[] = [$recapito['valore_contatto'], $recapito['id_contatto'] . "_" . $recapito['contatto_type']];
                        break;
                    }
                break;
                case 2: case 4:
                	if ( $recapito['check_mobile'] == 1 ) {
	                    switch( $recapito['use_type'] ){
	                        case 0: // messaggistica
	                            $sms_m[] = [$recapito['valore_contatto'], $recapito['id_contatto'] . "_" . $recapito['contatto_type']];
	                        break;
	                        case 2: // allerta
	                            $sms_a[] = [$recapito['valore_contatto'], $recapito['id_contatto'] . "_" . $recapito['contatto_type']];
	                        break;
	                    }
	                }
                break;
                case 3: case 5:
                	if ( $recapito['check_mobile'] != 1 ) {
	                    switch( $recapito['use_type'] ){
	                        case 0: // messaggistica
	                            $fax_m[] = [$recapito['valore_contatto'], $recapito['id_contatto'] . "_" . $recapito['contatto_type']];
	                        break;
	                        case 2: // allerta
	                            $fax_a[] = [$recapito['valore_contatto'], $recapito['id_contatto'] . "_" . $recapito['contatto_type']];
	                        break;
	                    }
	                }
                break;
            }
		}

		$n_record = 1;
		if ( count($email_a) / $max_email_a > 1 ) {
			
			$n = ceil ( count($email_a) / $max_email_a );
			if($n > $n_record) $n_record = $n;
		}

		if ( count($email_m) / $max_email_m > 1 ) {
			
			$n = ceil ( count($email_m) / $max_email_m );
			if($n > $n_record) $n_record = $n;
		}

		if ( count($sms_a) / $max_sms_a > 1 ) {
			
			$n = ceil ( count($sms_a) / $max_sms_a );
			if($n > $n_record) $n_record = $n;
		}

		if ( count($sms_m) / $max_sms_m > 1 ) {
			
			$n = ceil ( count($sms_m) / $max_sms_m );
			if($n > $n_record) $n_record = $n;
		}

		if ( count($fax_a) / $max_fax_a > 1 ) {
			
			$n = ceil ( count($fax_a) / $max_fax_a );
			if($n > $n_record) $n_record = $n;
		}

		if ( count($fax_m) / $max_fax_m > 1 ) {
			
			$n = ceil ( count($fax_m) / $max_fax_m );
			if($n > $n_record) $n_record = $n;
		}


		

		$return_elements = [];
		
		// n_record contiene il numero di record da dover mettere su everbridge per questo destinatario
		// per ognuno creo un nuovo record da mandare a everbridge
		for ( $nr = 0; $nr < $n_record; $nr++ ) {
			// accodo all'externalId il numero progressivo di questo record
			if($nr > 0) $base_record['externalId'] = $base_ext_id . "_" . $nr;

			$return_elements[] = $base_record;
		}

		// predispongo le query di insert per il contatto
		$insert_query = [];
		
		/**
		 * Per ogni canale inizio a popolare i record
		 */
		// -------------------- MAIL
		$n_m_a = 0;
		$curr_record = 0;
		foreach ($email_a as $mail_address) {
			if ( $n_m_a == $max_email_a ) {

				// resetta
				$n_m_a = 0;
				$curr_record++;
			}

			$return_elements[ $curr_record ]['paths'][] = [
					// n_m_a contiene l'indice corrente 
					"pathId" => $const['paths']['email']['allerta'][$n_m_a],
                    //"countryCode" => "IT",
                    "value" => $mail_address[0]
				];

			$insert_query[] = [ $mail_address[1], $return_elements[ $curr_record ]['externalId'],  $base_unformatted_ext_id, $const['paths']['email']['allerta'][$n_m_a] ];

			$n_m_a++;
		}


		$n_m_m = 0;
		$curr_record = 0;
		foreach ($email_m as $mail_address) {
			if ( $n_m_m == $max_email_m ) {
				// resetta
				$n_m_m = 0;
				$curr_record++;
			}

			$return_elements[ $curr_record ]['paths'][] = [
					// n_m_m contiene l'indice corrente 
					"pathId" => $const['paths']['email']['messaggistica'][$n_m_m],
                    //"countryCode" => "IT",
                    "value" => $mail_address[0]
				];

			$insert_query[] = [ $mail_address[1], $return_elements[ $curr_record ]['externalId'],  $base_unformatted_ext_id, $const['paths']['email']['messaggistica'][$n_m_m] ];

			$n_m_m++;
		}


		/**
		 * Per ogni canale inizio a popolare i record
		 */
		// ---------------------------------- FAX
		$n_f_a = 0;
		$curr_record = 0;
		foreach ($fax_a as $fax_address) {
			if ( $n_f_a == $max_fax_a ) {
				// resetta
				$n_f_a = 0;
				$curr_record++;
			}

			$return_elements[ $curr_record ]['paths'][] = [
					// n_f_a contiene l'indice corrente 
					"pathId" => $const['paths']['fax']['allerta'][$n_f_a],
                    "countryCode" => "IT",
                    "value" => $fax_address[0]
				];

			$insert_query[] = [ $fax_address[1], $return_elements[ $curr_record ]['externalId'],  $base_unformatted_ext_id, $const['paths']['fax']['allerta'][$n_f_a] ];

			$n_f_a++;
		}


		$n_f_m = 0;
		$curr_record = 0;
		foreach ($fax_m as $fax_address) {
			if ( $n_f_m == $max_fax_m ) {
				// resetta
				$n_f_m = 0;
				$curr_record++;
			}

			$return_elements[ $curr_record ]['paths'][] = [
					// n_f_m contiene l'indice corrente 
					"pathId" => $const['paths']['fax']['messaggistica'][$n_f_m],
                    "countryCode" => "IT",
                    "value" => $fax_address[0]
				];

			$insert_query[] = [ $fax_address[1], $return_elements[ $curr_record ]['externalId'],  $base_unformatted_ext_id, $const['paths']['fax']['messaggistica'][$n_f_m] ];

			$n_f_m++;
		}


		/**
		 * Per ogni canale inizio a popolare i record
		 */
		// ---------------------------------- SMS
		$n_s_a = 0;
		$curr_record = 0;
		foreach ($sms_a as $sms_address) {
			if ( $n_s_a == $max_sms_a ) {
				// resetta
				$n_s_a = 0;
				$curr_record++;
			}

			$return_elements[ $curr_record ]['paths'][] = [
					// n_s_a contiene l'indice corrente 
					"pathId" => $const['paths']['sms']['allerta'][$n_s_a],
                    "countryCode" => "IT",
                    "value" => $sms_address[0]
				];
			
			$insert_query[] = [ $sms_address[1], $return_elements[ $curr_record ]['externalId'],  $base_unformatted_ext_id, $const['paths']['sms']['allerta'][$n_s_a] ];

			$n_s_a++;
		}


		$n_s_m = 0;
		$curr_record = 0;
		foreach ($sms_m as $sms_address) {
			if ( $n_s_m == $max_sms_m ) {
				//echo "aumento il record\n";
				// resetta
				$n_s_m = 0;
				$curr_record++;
			}

			$return_elements[ $curr_record ]['paths'][] = [
					// n_s_m contiene l'indice corrente 
					"pathId" => $const['paths']['sms']['messaggistica'][$n_s_m],
                    "countryCode" => "IT",
                    "value" => $sms_address[0]
				];

			$insert_query[] = [ $sms_address[1], $return_elements[ $curr_record ]['externalId'],  $base_unformatted_ext_id, $const['paths']['sms']['messaggistica'][$n_s_m] ];

			$n_s_m++;
		}


		/**
		 * Aggiorno sul db il numero corrispondente in modo da sapere come inviare gli external id
		 * quando dovrò inviare al mas il numero di contatti in base a questo numero saprò come splittare
		 * @var [type]
		 */
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand("
		    DELETE FROM con_view_rubrica_everbridge_ext_ids WHERE identificativo = :id
			", [ ':id' => $base_unformatted_ext_id ]);

		$result = $command->queryAll();

		if(count($insert_query) > 0) { 
			$insertCount = Yii::$app->db->createCommand()
	                   	->batchInsert(
	                        'con_view_rubrica_everbridge_ext_ids', 
	                        ['contatto','ext_id','identificativo', 'delivery_path'], 
	                        $insert_query
	                    );                   	
	        /**
	         * Per gestire il conflitto del contatto (dato dall'identificativo già presente) 
	         * mettiamo che al conflict fa un update
	         * 
	         * in sostanza prima il contatto mas_rubrica_n poteva avere un identificativo operatore_pc_n
	         * per evitare conflitti tra gli effettivi operatori pc è stato rimosso 
	         * e come identificativo ora ha mas_rubrica_n
	         * 
	         * quelli inseriti in precedenza che sono stati sincronizzati su everbridge vanno adattati 
	         * generando conflitti,
	         * questo lo risolve
	         * @var [type]
	         */
	       	$sql = $insertCount->getRawSql();
	        $command = Yii::$app->db->createCommand( $sql . " 
	        ON CONFLICT (contatto) 
	        DO
	         UPDATE
	           SET 
	           identificativo = EXCLUDED.identificativo,
	           delivery_path = EXCLUDED.delivery_path,
	           ext_id = EXCLUDED.ext_id
	        ");
	        
	        
	        $command->execute();
	    }
        
		
		return $return_elements;

		
	}

	/**
	 * Aggiorna un record della rubrica
	 * 
	 * @param  [type] $identificativo: 
	 * ente_{id_ente} 
	 * struttura_{id_struttura} 
	 * organizzazione_{id_vol_organizzazione}
	 * {mas_rubrica_ruolo}_{id_mas_rubrica}
	 * 
	 * @return [type]         [description]
	 */
	public static function updateSingleContact ( $identificativo ) {
		
		// non mandiamo gli spazi a everbridge
		$parsed_identificativo = str_replace(" ", "_", $identificativo);
		$element_records = \common\models\ViewRubrica::find()->where(['identificativo'=>$identificativo])->all();

		
		if(count($element_records) < 1) return; 
		
		$elements = self::formatDestinatario( $element_records );
		

		$connection = Yii::$app->getDb();
		$command = $connection->createCommand("
		    SELECT DISTINCT ON (ext_id) ext_id FROM con_view_rubrica_everbridge_ext_ids WHERE identificativo = :id
			", [ ':id' => $identificativo ]);

		$result = $command->queryAll();
		$old_n = count($result);

		
		// ora in elements ho l'array
		// verifico che i precedenti siano minori agli attuali
		$new_count = count($elements);
		if ( $new_count < $old_n ) {
			Yii::error( ' elimino ext_id ', 'sync' );
			// se i nuovi sono meno devo cancellarli da everbridge
			// l'external id è identificativo_{n-1} ( es il secondo è identificativo_1 )
			$external_id_to_delete = [];
			// es. 
			// prima erano 4
			// ora sono 2
			// su everbridge avrò 
			// 	identificativo
			// 	identificativo_1
			// 	identificativo_2
			// 	identificativo_3
			// 	
			// devo cancellare 
			// 	identificativo_3
			// 	identificativo_2
			for ( $start = $old_n-1; $start > $new_count-1; $start-- ) {
				// 3 > 2-1
				// 2 > 2-1
				// 1 = 2-1
				$external_id_to_delete[] = ($start > 1) ? $parsed_identificativo . "_" . $start : $parsed_identificativo;
			}

			self::deleteExtIds( $external_id_to_delete );
		}

		self::updateContacts( $elements );

	}

	/**
	 * Inserisci/aggiorna i contatti per un destinatario
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public static function updateContacts( $array ) {
		
		$endpoint = 'https://api.everbridge.net/rest/contacts/'.Yii::$app->params['everbridge']['EVERBRIDGE_ORGANIZATION_ID'].'/batch';
		$caller = new \GuzzleHttp\Client( self::getGuzzleOptions() );
        
        try {
        	$response = $caller->request('POST', $endpoint, [
                    'auth' => [
                        Yii::$app->params['everbridge']['EVERBRIDGE_USER'], 
                        Yii::$app->params['everbridge']['EVERBRIDGE_PASSWORD']
                    ],
                    'json' => $array,//$record_to_add,
                ]);
        	
        	Yii::info( json_encode($array) , 'sync' );
        	Yii::info( 'https://api.everbridge.net/rest/contacts/'.Yii::$app->params['everbridge']['EVERBRIDGE_ORGANIZATION_ID'].'/batch' , 'sync' );
        	Yii::info( Yii::$app->params['everbridge']['EVERBRIDGE_USER'] , 'sync' );
        	Yii::info( Yii::$app->params['everbridge']['EVERBRIDGE_PASSWORD'] , 'sync' );

        	$json = json_decode( $response->getBody(), true);
        	// non si capisce perchè l'ok ha codice 100 invece di 200 o 201
        	if($json['code'] != 100) {
        		throw new \Exception(implode(", ", $json['data'] ), 1);
        	}
        	

        } catch ( \Exception $e ) {
        	Yii::error( $e->getMessage(), 'sync' );
        	throw new \Exception($e->getMessage(), 1);
        }
	}

	/**
	 * Cancella external id non più necessari
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public static function deleteExtIds( $array ) {
		$endpoint = 'https://api.everbridge.net/rest/contacts/' . Yii::$app->params['everbridge']['EVERBRIDGE_ORGANIZATION_ID'] . '/batch?idType=externalId';
		$caller = new \GuzzleHttp\Client( self::getGuzzleOptions() );
        
		try {
        	$response = $caller->request('DELETE', $endpoint, [
                'auth' => [
                    Yii::$app->params['everbridge']['EVERBRIDGE_USER'], 
                    Yii::$app->params['everbridge']['EVERBRIDGE_PASSWORD']
                ],
                'json' => $array
            ]);

        } catch( \Exception $e ) {
        	Yii::error( $e->getMessage() );
        }

	}

	/**
	 * Ritorna i contatti dati gli ext_id
	 * @param  array $ext_ids array di external id su everbridge
	 * @return [type]          [description]
	 */
	public static function getInfoFromEverbridge( $ext_ids ) {

		if(empty($ext_ids) || !is_array($ext_ids)) return "External id non validi";

		$clean_ext_ids = [];
		foreach ($ext_ids as $e) {
			if(!empty($e)) $clean_ext_ids[] = $e;
		}

		if(empty($clean_ext_ids)) return "Nessun dato";
		

		$endpoint = 'https://api.everbridge.net/rest/contacts/'.Yii::$app->params['everbridge']['EVERBRIDGE_ORGANIZATION_ID'].'/?externalIds='.implode(",",$ext_ids);
		$caller = new \GuzzleHttp\Client( self::getGuzzleOptions() );
        
        try {
        	$response = $caller->request('GET', $endpoint, [
                    'auth' => [
                        Yii::$app->params['everbridge']['EVERBRIDGE_USER'], 
                        Yii::$app->params['everbridge']['EVERBRIDGE_PASSWORD']
                    ],
                    'json' => [
                    	'externalIds' => implode(",", $ext_ids)
                    ]
                ]);
        	
        	
        	$response = json_decode( $response->getBody()->getContents(), true );

        	$data = [];
        	if(isset($response['page']['data'])) {
	        	foreach ($response['page']['data'] as $element) {
	        		foreach ($element['paths'] as $deliveryPath) {
	        			$data[] = [
	        				'externalId' => $element['externalId'],
	        				'contact' => $deliveryPath['value'],
	        				'path' => self::replaceDeliveryPathById( $deliveryPath['pathId'] )
	        			];
	        		}
	        	}
	        }

        	$response = $data;


        } catch ( \Exception $e ) {
        	Yii::error( $e->getMessage(), 'sync' );
        	$response = $e->getMessage();	
        }

        return $response;
	} 


	private static function replaceDeliveryPathById( $pathId ) {
		foreach (Yii::$app->params['everbridge']['const_configuration']['paths'] as $key => $path) {
			
				foreach ($path['allerta'] as $path_id) {
					if($pathId == $path_id) return 'Allerta ' . $key;
				}
				foreach ($path['messaggistica'] as $path_id) {
					if($pathId == $path_id) return 'Messaggistica ' . $key;
				}
		}
	}
}