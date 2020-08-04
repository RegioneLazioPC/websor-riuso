<?php 
namespace common\utils;

use common\models\AlmAllertaMeteo;
use common\models\MasInvio;
use common\models\MasMessage;
use common\models\MasMessageTemplate;
use common\models\UplMedia;

use common\utils\MasMessageManager;

use Yii;
use yii\base\Exception;

/**
 * Gestione dell'invio del messaggio o allerta al modulo MAS
 */
class MasDispatcher {	

	protected $contacts;
	
	/**
	 * [
	 * 	'Email' => ['title'=>'', 'text'=>'']
	 * 	'Fax' => ['title'=>'', 'text'=>'']
	 *  ...
	 * ]
	 * @var array
	 */
	protected $id_invio;
	protected $invio;
	protected $message = [];
	protected $files = [];

	protected $channels = [
		'Email',
		'Pec',
		'Sms',
		'Push',
		'Fax'
	];

	protected $status_channels = [
        'Email' => 'status_mail',
        'Pec' => 'status_pec',
        'Sms' => 'status_sms',
        'Push' => 'status_push',
        'Fax' => 'status_fax',
    ];

    private function replaceStatusChannel( $channel ) {
        return $this->status_channels[$channel];
    }

	protected $grouped = false;

	public function __construct( array $contacts, $invio ) {
		$this->contacts = $contacts;
		$this->invio = $invio;
		$this->id_invio = $invio->id;		
	}

	protected function setMessage( $channel ) {
		$this->files = [];
		$message = MasMessage::findOne( $this->invio->id_message );
		$allerta = AlmAllertaMeteo::findOne( $message->id_allerta );
		$template = null;
		if( !empty( $message->id_template ) ) $template = MasMessageTemplate::findOne( $message->id_template );

		/**
		 * Titolo di default se non presente allerta o meno
		 * @var def_title
		 */
		$def_title = ($allerta) ? 
		"Allerta del ".date( "d/m/Y", strtotime( $allerta->data_allerta ) ) :
		"Messaggio Protezione Civile";


		if( $channel == 'all' ) {
			foreach ($this->channel as $ch ) {
				$this->message[$ch] = [ 
					'title'=> ($message->title != '') ? $message->title : $def_title,
					'text'=> MasMessageManager::getPreview( $message, $template, $this->getChannelIndex( $ch ) ),
					'type' => ($allerta) ? 'allerta' : 'messaggio'
				];
			}			
		} else {
			if( in_array( $channel, $this->channels ) ) {
				$this->message[$channel] = [
					'title'=> ($message->title != '') ? $message->title : $def_title,
					'text'=> MasMessageManager::getPreview( $message, $template, $this->getChannelIndex( $channel ) ),
					'type' => ($allerta) ? 'allerta' : 'messaggio'
				];
			}
		}

		
		// Inserisco file multipli
		if(!empty($allerta->file)) {
			foreach ($allerta->file as $media) {
				$base_path = Yii::getAlias('@backend');
				$file_path = $base_path . '/uploads/' . $media->ext . '/' . $media->date_upload . '/' . $media->nome;
				$this->files[] = [
					'id' => $media->id,
					'mime' => $media->mime_type,
					'ext' => $media->ext,
					'path'=>$file_path
				];
			}
		}

		if(!empty($message->file)) {
			foreach ($message->file as $media) {
				$base_path = Yii::getAlias('@backend');
				$file_path = $base_path . '/uploads/' . $media->ext . '/' . $media->date_upload . '/' . $media->nome;
				$this->files[] = [
					'id' => $media->id,
					'mime' => $media->mime_type,
					'ext' => $media->ext,
					'path'=>$file_path
				];
			}
		}

	}

	protected function getChannelIndex( $channel ) {
		switch( $channel ){
			case 'Email': return 0; break;
			case 'Pec': return 1; break;
			case 'Fax': return 2; break;
			case 'Sms': return 3; break;
			case 'Push': return 4; break;
		}
	}


	/**
	 * Inizializza un nuovo messaggio da mettere in coda
	 * @return array
	 */
	public function initialize() {

		$res = ['errors'=>[],'success'=>[]];

		foreach ($this->channels as $channel) {
			$this->setMessage( $channel );

			$contacts = array_filter ( $this->contacts, function($array) use ($channel) {
				return $array['channel'] == $channel;
			} );	

			if( count($contacts) > 0 ) {
				
				$base_call = (@Yii::$app->params['base_mas_callback']) ? Yii::$app->params['base_mas_callback'] : Yii::$app->urlManagerApi->getBaseUrl();

				$dt = [
					'message' => $this->message[$channel],
					'contacts' => array_values($contacts),
					'channel' => $channel,
					'files' => $this->files,
					'id_invio' => $this->id_invio,
					'callback' => $base_call . '/mas/' . $this->id_invio . '/' . Yii::$app->params['mas_token']
				];

				$files = [];
				foreach ($this->files as $file) {
					$files[] = [
						'name' => 'attachment[]',
						'filename' => $file['id'].'.'.$file['ext'],
						'contents' => fopen( $file['path'], 'r' )
					];
				}


				$guzzle_options = [];
				

				$client = new \GuzzleHttp\Client( $guzzle_options );
				$endpoint = Yii::$app->params['mas_host'] . 'process';		
		
				$temp = tmpfile();
				fwrite($temp, json_encode($dt));
				
				$files[] = [
					'name' => 'message[]',
					'mime' => 'application/json',
					'ext' => 'json',
					'filename' => 'message.json',
					'contents' => fopen( stream_get_meta_data($temp)['uri'] , 'r' )
				];
						
			    $response = $client->request('POST', $endpoint, 
			        [
			        	'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']],
			        	'multipart' => $files,
			        ]);

			    fclose($temp); 
			    

			    $status_channel = $this->replaceStatusChannel( $channel );

			    if($response->getStatusCode() != 200) {
			    	
			    	$body = $response->getBody()->getContents();			    	
			    	Yii::error($response->getBody());

			    	
			    	$res['errors'][] = [
			    		'status_channel'=>$status_channel
			    	];

			    } else {
			    	$body = $response->getBody()->getContents();

			    	/**
			    	 * Aggiorno l'invio
			    	 * @var [type]
			    	 */
			    	$id = json_decode($body, true)['_id'];
			    	$res['success'][] = [
			    		'status_channel'=>$status_channel,
			    		'_id' => $id
			    	];

			    }

			    
				
			}

		}

		return $res;

	}


	public static function getInvioStatus( $id_invio ) {
		
		$guzzle_options = [];
		$client = new \GuzzleHttp\Client( $guzzle_options );
		
		

		try {
			$endpoint = Yii::$app->params['mas_host'] . 'message/working/' . $id_invio;
			$response = $client->request('GET', $endpoint, [
				'auth' => [Yii::$app->params['mas_username'], Yii::$app->params['mas_password']]
			]);
			return $response->getBody()->getContents();
		} catch ( \Exception $e ) {
			Yii::error($e->getMessage());			
			return false;
		}		

	}

		
}