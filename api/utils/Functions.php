<?php 
namespace api\utils;

class Functions {

    public static function getRandomString( $length ) {

    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	    
    }

    public static function file_upload_max_size() {
	  	return min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
	}

	public static function convertPHPSizeToBytes() {
		$size = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
		$sSuffix = strtoupper(substr($size, -1));
	    if (!in_array($sSuffix,array('P','T','G','M','K'))){
	        return (int)$size;  
	    } 
	    $iValue = substr($size, 0, -1);
	    switch ($sSuffix) {
	        case 'P':
	            $iValue *= 1024;
	            // Fallthrough intended
	        case 'T':
	            $iValue *= 1024;
	            // Fallthrough intended
	        case 'G':
	            $iValue *= 1024;
	            // Fallthrough intended
	        case 'M':
	            $iValue *= 1024;
	            // Fallthrough intended
	        case 'K':
	            //$iValue *= 1024;
	            break;
	    }
	    return (int)$iValue;
	}

}