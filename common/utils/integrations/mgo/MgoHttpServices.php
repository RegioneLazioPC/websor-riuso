<?php

namespace common\utils\integrations\mgo;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Yii;

use common\models\app\AppSyncErrorLog;

class MgoHttpServices
{
    private static $cacheKey = 'jwtToken';

    /**
     * Check cached token 
     * @return bool   
     */
    protected static function getCachedToken()
    {

        $cachedToken = Yii::$app->cache->get(self::$cacheKey);

        if (empty($cachedToken)) {
            
            self::login();
            $cachedToken = Yii::$app->cache->get(self::$cacheKey);
            
            if (empty($cachedToken)) throw new \Exception('No token in cache');
        }

        // if valid a Lcobucci\JWT\Token is returned
        $token = Yii::$app->jwt->getParser()->parse((string) $cachedToken);

        if (!empty($token) && $token->getClaim('exp') > time()) {
        
            return $cachedToken;
        
        } else {
            
            self::login();
            $cachedToken = Yii::$app->cache->get(self::$cacheKey);
            
            if (empty($cachedToken)) return null;
            
            return $cachedToken;
        }
    }

    /**
     * Auth service
     * @param  [type] $record [description]
     * @return [array]        Ritorna un array con uno o più record corrispondenti a questo destinatario
     */
    public static function login()
    {
        $client = new Client();
        $response = $client->request('POST', Yii::$app->params['mgo_api_base_url'] . 'v1/auth/auth/login', [
            'json' => ['username' => Yii::$app->params['mgo_api_username'], 'password' => Yii::$app->params['mgo_api_password']]
        ]);

        if ($response->getReasonPhrase() == 'OK') {

            // Get response and parse token
            $bodyResponse = $response->getBody()->getContents();
            $parsedBody = json_decode($bodyResponse, true);
            $token = $parsedBody['data']['token'];
            $tokenDecoded = Yii::$app->jwt->getParser()->parse((string) $token);

            // Set token in cache
            if (!empty($token)) {
                Yii::$app->cache->set(
                    self::$cacheKey,
                    $token,
                    $tokenDecoded->getClaim('exp') - time() // set the cache duration oriented by the expiration claim
                );
            } else {
                AppSyncErrorLog::createError('login', "Token assente da responso login mgo");
            }

            return $token;
        } else {
            AppSyncErrorLog::createError('login', $response->getBody()->getContents());
        }
        return null;
    }

    /**
     * Add convenzione
     * @param  [type] $record [description]
     * @return [array]        Ritorna un array con uno o più record
     */
    public static function addConvenzione($codIstat)
    {
        $token = self::getCachedToken();
        $cookieJar = CookieJar::fromArray([
            'Authorization' => "Bearer " . $token
        ], Yii::$app->params['mgo_api_cookie_domain']);

        $client = new Client();
        $response = $client->request('POST', Yii::$app->params['mgo_api_base_url'] . 'interoperabilita/organizzazione/convenzione', [
            //'cookies' => $cookieJar,
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ],
            'query' => ['codistat' => $codIstat]
        ]);

        if ($response->getReasonPhrase() == 'OK') {
            $bodyResponse = $response->getBody()->getContents();
            $parsedBody = json_decode($bodyResponse, true);
            return $parsedBody;
        } else {
            throw new \Exception('Error api');
        }
    }

    /**
     * List ODV
     * @param  [type] $record [description]
     * @return [array]        Ritorna un array con uno o più record
     */
    public static function getOdv($page = 1)
    {
        $token = self::getCachedToken();
        $cookieJar = CookieJar::fromArray([
            'Authorization' => "Bearer " . $token
        ], Yii::$app->params['mgo_api_cookie_domain']);

        $client = new Client();
        $response = $client->request('GET', Yii::$app->params['mgo_api_base_url'] . 'interoperabilita/organizzazione', [
            //'cookies' => $cookieJar,
            'query' => ['per_page' => 250, 'page' => $page],
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ]
        ]);

        if ($response->getReasonPhrase() == 'OK') {
            $bodyResponse = $response->getBody()->getContents();
            $parsedBody = json_decode($bodyResponse, true);
            return $parsedBody;
        } else {
            AppSyncErrorLog::createError('odv', $response->getBody()->getContents());
            throw new \Exception('Error api');
        }
    }

    /**
     * List Volontario
     * @param  [type] $record [description]
     * @return [array] Ritorna un array con uno o più record
     */
    public static function getVolontario($page = 1)
    {
        $token = self::getCachedToken();
        $cookieJar = CookieJar::fromArray([
            'Authorization' => "Bearer " . $token
        ], Yii::$app->params['mgo_api_cookie_domain']);

        $client = new Client();
        $response = $client->request('GET', Yii::$app->params['mgo_api_base_url'] . 'interoperabilita/volontario', [
            //'cookies' => $cookieJar,
            'query' => ['per_page' => 250, 'page' => $page, 'codistat_comune' => Yii::$app->FilteredActions->comune->codistat],
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ]
        ]);

        if ($response->getReasonPhrase() == 'OK') {
            $bodyResponse = $response->getBody()->getContents();
            $parsedBody = json_decode($bodyResponse, true);
            return $parsedBody;
        } else {
            AppSyncErrorLog::createError('volontario', $response->getBody()->getContents());
            throw new \Exception('Error api');
        }
    }

    /**
     * List Risorse
     * @param  [type] $record [description]
     * @return [array] Ritorna un array con uno o più record
     */
    public static function getRisorsa($page = 1)
    {
        $token = self::getCachedToken();
        $cookieJar = CookieJar::fromArray([
            'Authorization' => "Bearer " . $token
        ], Yii::$app->params['mgo_api_cookie_domain']);

        $client = new Client();
        $response = $client->request('GET', Yii::$app->params['mgo_api_base_url'] . 'interoperabilita/risorsa', [
            //'cookies' => $cookieJar,
            'query' => ['per_page' => 250, 'page' => $page, 'codistat_comune' => Yii::$app->FilteredActions->comune->codistat],
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ]
        ]);

        if ($response->getReasonPhrase() == 'OK') {
            $bodyResponse = $response->getBody()->getContents();
            $parsedBody = json_decode($bodyResponse, true);
            return $parsedBody;
        } else {
            AppSyncErrorLog::createError('risorsa', $response->getBody()->getContents());
            throw new \Exception('Error api');
        }
    }
}
