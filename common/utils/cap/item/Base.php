<?php 
namespace common\utils\cap\item;
use Yii;
class Base {

	public $cap_formatted_item;
	public $url;
	public $profile;
	public $plain_content;

	public function __construct( $url, string $profile, array $headers, array $options ) {
		
		$this->url = (string) $url;
		$this->profile = $profile;

		try {

			$client = new \GuzzleHttp\Client( $options );
        	$res = $client->request('GET', (string) $this->url, $headers);

	        $status = $res->getStatusCode();
	        if($status != 200) throw new \Exception("Errore chiamata cap feed url " . $this->url, 1);
	        
	        $this->plain_content = $res->getBody();
            $x = preg_replace("/<contact>(.*)<\/contact>/", "<contact></contact>", $this->plain_content);
            $xml = new \SimpleXMLElement($x);

            $this->loadFeedData($xml);

	    } catch(\Exception $e) {
	    	Yii::error("ERRORE PARSING FEED URL: " . $this->url);
	    	throw $e;
	    }

	}

	/**
	 * Carica i dati del feed in base al dialetto
	 * @param  [type] $xml [description]
	 * @return [type]      [description]
	 */
	protected function loadFeedData($xml) {
		switch ( strtolower( $this->profile ) ) {
			case 'vvf':
				$this->cap_formatted_item = new Vvf($xml);
				break;
			
			default:
				$this->cap_formatted_item = new Standard($xml);
				break;
		}
	}

	public static function getBaseItemFromProfile( $profile, $xml_data ) {
		$x = preg_replace("/<contact>(.*)<\/contact>/", "<contact></contact>", $xml_data);
        $xml = new \SimpleXMLElement($x);
		switch ( strtolower( $profile ) ) {
			case 'vvf':
				return new Vvf($xml);
				break;
			
			default:
				return new Standard($xml);
				break;
		}
	}
}
