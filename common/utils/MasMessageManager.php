<?php
namespace common\utils;

use common\models\MasMessage;
use common\models\MasMessageTemplate;

/**
 * Costruzione dei template di messaggi e allerta con sostituzione tag
 */
class MasMessageManager
{
	protected $message;
	protected $template;
	
	public function __construct(MasMessage $message, $template)
	{
		$this->message = $message;
		$this->template = $template;
	}

	/**
	 * Imposta messaggio
	 * @param MasMessage $message 
	 */
	public function setMessage(MasMessage $message) 
	{
		$this->message = $message;
	}

	/**
	 * Imposta template
	 * @param MasMessageTemplate $template 
	 */
	public function setTemplate( $template )  
	{
		$this->template = @$template;
	}

	/**
	 * Preview
	 * @param  string $message  
	 * @param  MasMessageTemplate $template 
	 * @param  integer $channel  
	 * @return MasMessageManager         
	 */
	public static function getPreview( $message, $template, $channel ) 
	{
		return (new self( $message, $template ))->buildFullPreview($channel);
	}

	/**
	 * Costruisci preview del messaggio
	 * @param  integer $channel
	 * @return string          
	 */
	private function buildFullPreview( $channel )
	{
		switch ( $channel ) {
			case 0: case 1: //'Email'
				return $this->returnParsedMessage($this->message->mail_text, @$this->template->mail_body, false);
			break;
			case 2: //'Fax'
				return $this->returnParsedMessage($this->message->fax_text, @$this->template->fax_body, false);
			break;
			case 3://'Sms'
				return $this->returnParsedMessage($this->message->sms_text, @$this->template->sms_body);
			break;
			case 4://'Push'
				return $this->returnParsedMessage($this->message->push_text, @$this->template->push_body);
			break;
		}
	}

	/**
	 * Ritorna messaggio parsato
	 * @param  string  $message 
	 * @param  string  $body    
	 * @param  boolean $replace 
	 * @return string         
	 */
	private function returnParsedMessage( $message, $body, $replace = false ) 
	{
		
		if(!empty($body) && $body != "") return ($replace) ? 
			$this->replaceImages( $this->replaceTags( $message, $body )) : 
			$this->replaceTags( $message, $body );

		return $message;
	}

	/**
	 * Sostituisci i tag nel messaggio
	 * @param  string $message 
	 * @param  string $body    
	 * @return string
	 */
	private function replaceTags( $message, $body) {
		if( !empty( $this->message->allerta ) ) {

			$body = str_replace("{{data_allerta}}", \Yii::$app->formatter->asDate( $this->message->allerta->data_allerta), $body);
		}

		return str_replace("{{message}}", $message, $body);
	}

	/**
	 * @deprecated
	 * 
	 */
	private function replaceImages( $text ) {
		
		libxml_use_internal_errors(true);

		$dom = new \DOMDocument();
		$dom->loadHTML( $text );

		foreach ($dom->getElementsByTagName('img') as $img) {
		    $src = $img->getAttribute('src');
		    $img->setAttribute( 'src', $this->data_uri( $src ) );
		    $img->setAttribute( 'style', "max-width: 100%" );
		}
		
		$content = $dom->saveHTML();
		
		return $content;
	}

	/**
	 * @deprecated
	 * 
	 */
	private function data_uri($filename) {
		try {
		    $mime = $this->get_mime( exif_imagetype( $filename ) );
		    // se non immagine ritorna vuoto
		    if(!$mime) return "data:image/png;base64,";
		    
		    $data = base64_encode(file_get_contents($filename));

		    return "data:$mime;base64,$data";
		} catch ( \Exception $e ) {
			echo $e->getMessage();
			throw new \Exception("Error Processing Request", 1);
		}
	}

	/**
	 * @deprecated
	 * @param  integer $index 
	 * @return string|boolean
	 */
	private function get_mime($index){
		try {
		    switch($index) {
		    	case 1:
		    	return "image/gif";
		    	break;
		    	case 2:
		    	return "image/jpeg";
		    	break;
		    	case 3:
		    	return "image/png";
		    	break;
		    	default:
		    	return false;
		    	break;
		    }
		} catch( \Exception $e ) {
			return false;
		}
	}


	/**
	 * Metodo pubblico per avere la formattazione indipendentemente dal tipo di messaggio
	 * @param  string $text 
	 * @param  string $body
	 * @return string
	 */
	public static function returnPlainReplacedMessage( $message, $body, $data_allerta = null ) {
		if(!empty($body) && $body != "") {
			$body = ( !empty( $data_allerta ) ) ? str_replace("{{data_allerta}}", \Yii::$app->formatter->asDate( $data_allerta ), $body) : $body;
			return str_replace("{{message}}", $message, $body);
		} 
			
		return $message;
	}
	

}