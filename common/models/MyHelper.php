<?php

namespace common\models;
use Exception;
use Imagick;
use yii\base\Model;
use Yii;


class MyHelper extends Model
{
    /**
     * Loop elementi in view del pdf
     * @return [type] [description]
     */
    public static function loopPdfElements($el, $parent, $main, $echoed_main, $added_names)
    {

        $childs = "";
        $has_values = false;
        $has_images = false;

        if(isset($el['value']) && $el['value'] != "") {
            $has_values = true;
            $childs .= '<p><span>'.$el['name'].'</span>: '.$el['value'].'</p>';
        }

        if(isset($el['photos']) && $el['photos'] != "") {
            foreach ($el['photos'] as $photo) {
                $has_photos = true;
                $childs .= '<img src="'.$photo.'" style="max-height: 300px;" />';
            }
        }


            
        if($childs != "") {
            if(!$echoed_main) {
                $echoed_main = true;
                echo '<div style="page-break-before: always"></div><h3>'.$main['name'].'</h3>';
            }

            if(!in_array($el['name'], $added_names) && !$has_values)
            { 
                $added_names[] = $el['name'];
                echo '<h4>'.$el['name'].'</h4>';
            }
            
            echo $childs;
        }

        if(isset($el['items']) && count($el['items']) > 0) {
            foreach ($el['items'] as $item) {
                MyHelper::loopPdfElements($item, $el, $main, $echoed_main, $added_names);
            }
        } 
    }
    
    /**
     * Cerca su google le coordinate della località e scorre i risultati fino a prendere il primo 
     * tra quelli ubicati nella regione di appartenenza
     * @param in string $address
     * @return array|bool
     */
    public static function getLatLonFromAddress($address){

        // Geocoding

        // url encode the address
        $comune = '';
        $address = urlencode(strtolower($address));
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&region=it&language=it&components=country:IT&key=".Yii::$app->params['google_key'];
        
        if(isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) :

            $proxy = Yii::$app->params['proxyUrl'];

            $context = array(
                'http' => array(
                    'proxy' => $proxy,
                    'request_fulluri' => True,
                    ),
                );

            $context = stream_context_create($context);
            $resp_json = file_get_contents($url, false, $context);
        else:

            try {
                $resp_json = file_get_contents($url);
            } catch (\Exception $e) {
                // Handle exception
                return false;
            }

        endif;
        
        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){

            // Scorre i vari risultati per catturare il primo eventuale collocato nella regione
            foreach ($resp['results'] as $key=> $result) {
                $found = false;
                foreach ($result['address_components'] as $address) {
                    
                    if ($address['types'][0]=='country' && strtoupper($address['short_name'])=='IT') :
                        $found = true;
                        break;
                    endif;
                    
                }
            }
            if (!$found) return false;
            
            // get the important data
            $lat = $resp['results'][$key]['geometry']['location']['lat'];
            $lon = $resp['results'][$key]['geometry']['location']['lng'];

            if(isset($resp['results'][$key]['address_components'])) {
                foreach ($resp['results'][$key]['address_components'] as $res) {
                    if( isset($res['types']) && isset($res['types'][0]) && $res['types'][0] == 'administrative_area_level_3' ) {
                        $comune = $res['long_name'];
                    }
                }
            }

            
            if($lat && $lon){

                return array('lat' => $lat, 'lon' => $lon, 'comune'=>$comune);

            }else{
                return false;
            }

        }else{
            return false;
        }

    }


    public static function getAddressFromLatLon($lat, $lon){

        // Geocoding
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lon}&key=".Yii::$app->params['google_key'];
        Yii::info('Chiamo URL GOOGLE: ' . $url, 'api');
        $proxy = (isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) ? Yii::$app->params['proxyUrl'] : null;

        try {

            //$resp_json = file_get_contents($url);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $data = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_errno > 0) {
                Yii::info("Errore curl: " . $curl_errno, 'api');
                throw new \Exception("Errore curl: " . $curl_errno, 1);
            } else {
                $resp_json = $data;
            }

        } catch (\Exception $e) {
            return false;
        }

        // get the json response
        
        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to reverse geocode given lat e lon
        if($resp['status']=='OK'){
            Yii::error('Reverse geocode OK');
            return $resp;
        }else{
            Yii::error('Reverse geocode KO');
            return false;
        }
    }


    /**
     * Cerca le coordinate dei parametri località, comune, indirizzo e restituisce i dati coerenti 
     * delle coordinate e della località, comune, indirizzo. 
     * In caso di errore restituisce nell'array il messaggio e il codice di errore.
     * 
     * @param in array(luogo,idcomune,indirizzo,lat,lon) I parametri mancanti vengono inizializzati a NULL
     * @return array(luogo,idcomune,indirizzo,lat,lon)|[error=>[msg,code]]
     * @throws \Exception
     */
    public static function getCoordinateLocalita($data) {
        // Riorganizza i parametri di ingresso

        $data = array_merge(['idcomune'=> NULL, 'indirizzo'=> NULL, 'luogo'=> NULL, 'lat'=> NULL, 'lon'=> NULL], $data);

        // Inizializza l'uscita
        $res['luogo'] = '';
        $res['idcomune'] = NULL;
        $res['indirizzo'] = '';
        $res['lat'] = Yii::$app->params['lat'];
        $res['lon'] = Yii::$app->params['lng'];
        
        $comune = !empty($data['idcomune']) ? LocComune::findOne($data['idcomune']) : new LocComune;        
        if (!empty($data['indirizzo'])) {
            
            $indirizzo_completo = $data['indirizzo'];
            if($comune->id) :
                $pr = $comune->provincia->sigla;
                $cm = $comune->comune;
                if(!preg_match("/$cm/", $data['indirizzo'])) :
                    $indirizzo_completo .= ', ' . $comune->comune.' '.$comune->provincia->sigla;
                endif;
            endif;
            
            
            $geoCoordinates = MyHelper::getLatLonFromAddress($indirizzo_completo);

            
            if (!$geoCoordinates) return ['error'=>['msg'=>"Non ho trovato le coordinate del comune. Ricontrollare i dati inseriti o inserire manualmente latitudine e longitudine", 'code'=>505]];
            $res['lat'] = $geoCoordinates['lat'];
            $res['lon'] = $geoCoordinates['lon'];

            if(!$comune->id && isset($geoCoordinates['comune']) && $geoCoordinates['comune'] != '' && empty($data['idcomune']) ) {
                
                $cm = LocComune::find()->where(['comune'=>$geoCoordinates['comune']])->one();
                
                if($cm) : $res['idcomune'] = $cm->id; endif;
            } elseif(isset($comune->id)) {
                $res['idcomune'] = $comune->id;
            } else {
                return ['error'=>['msg'=>"Non ho trovato le coordinate del comune. Ricontrollare i dati inseriti o inserire manualmente latitudine e longitudine", 'code'=>505]];
            }

            $res['indirizzo'] = $indirizzo_completo;
            
        } elseif (!empty($data['luogo'])) {

            $str = $data['luogo'];
            
            if($comune->id) $str .= ', '.$comune->comune . ', '.$comune->provincia->sigla;
            $geoCoordinates = MyHelper::getLatLonFromAddress($str);
            
            if (!$geoCoordinates) {
                return ['error'=>['msg'=>"Non ho trovato le coordinate della località inserita. Ricontrollare i dati inseriti o inserire manualmente latitudine e longitudine", 'code'=>506]];
            }

            $res['lat'] = $geoCoordinates['lat'];
            $res['lon'] = $geoCoordinates['lon'];
            $res['luogo'] = $data['luogo'];
            
            if($comune && $comune->id) : $res['idcomune'] = $comune->id; endif;
            
            if(isset($geoCoordinates['comune']) && $geoCoordinates['comune'] != '' && empty($data['idcomune']) ) {
                
                $cm = LocComune::find()->where(['comune'=>$geoCoordinates['comune']])->one();
                
                if($cm) : $res['idcomune'] = $cm->id; endif;
            }

        } elseif (empty($data['luogo']) && empty($data['indirizzo']) && $comune->id) {

            $str = $comune->comune.', '.$comune->provincia->sigla;
            $geoCoordinates = MyHelper::getLatLonFromAddress($str);
            
            if (!$geoCoordinates) :
                return ['error'=>['msg'=>"Non ho trovato le coordinate della località inserita. Ricontrollare i dati inseriti o inserire manualmente latitudine e longitudine", 'code'=>506]];
            endif;
            
            $res['lat'] = $geoCoordinates['lat'];
            $res['lon'] = $geoCoordinates['lon'];
            $res['luogo'] = $data['luogo'];
            
            $res['idcomune'] = $comune->id;
            
        } elseif(empty($data['luogo']) && empty($data['indirizzo']) && !$comune)
        {
            return ['error'=>['msg'=>"Non ho trovato le coordinate del comune. Ricontrollare i dati inseriti o inserire manualmente latitudine e longitudine", 'code'=>505]];
        }
        return $res;
    }

    /**
     * @return string
     * @deprecated
     */
    public static function generateSmsCode()
    {
        //Generate SMS Code
        $randomString = self::generateRandomString();
        $rndNumber = rand('1000', '9999');
        $firstChar = substr($randomString, 0, 1);
        $lastChar = substr($randomString, -1);
        $smsCode = $firstChar.$rndNumber.$lastChar;

        // Check if smsCode exist in utl_utente and recall self method
        $utente = UtlUtente::findOne(['smscode' => $smsCode]);
        if(!empty($utente)){
            error_log('Sms Code Exist recall function generateSmsCode()');
            self::generateSmsCode();
        }

        return $smsCode;
    }

    /**
     * Funzione ricorsiva per la generazione univoca del codice sms
     * Controllo sulla tabella utl_utente per verifica se codice generato già esiste
     * @return string
     */
    public static function  generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @deprecated
     * @param  [type] $sourcePath      [description]
     * @param  [type] $destinationPath [description]
     * @param  [type] $colors          [description]
     * @return [type]                  [description]
     */
    public static function convertSvgToPng($sourcePath, $destinationPath, $colors=null){
        $usmap = $sourcePath;
        $im = new Imagick();
        $svg = file_get_contents($usmap);

        if(!empty($colors) && is_array($colors)) {

            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = False;
            $doc->loadXML($svg) or die('Failed to load SVG file ' . $svg . ' as XML.  It probably contains malformed data.');

            
            $AllTags = $doc->getElementsByTagName("path");
            foreach ($AllTags as $ATag) {

                foreach ($colors as $index => $color) {
                    if ($ATag->getAttribute('id') == 'cala'.$index) {
                        $ATag->setAttribute('fill', $color['color']);
                    }
                }

                $svg = $doc->saveXML($doc);

            }
        }

        $im->readImageBlob($svg);

        /*png settings*/
        $im->setImageFormat("png24");
        $im->resizeImage(108, 204, Imagick::FILTER_LANCZOS, 1);  /*Optional, if you need to resize*/

        /*jpeg*/
        $im->setImageFormat("jpeg");
        $im->adaptiveResizeImage(108, 204); /*Optional, if you need to resize*/

        $path=dirname($destinationPath);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $im->writeImage($destinationPath);/*(or .jpg)*/
        $im->clear();
        $im->destroy();
    }

}