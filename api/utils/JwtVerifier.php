<?php

namespace api\utils;

use yii\di\Instance;
use sizeg\jwt\Jwt;

class JwtVerifier
{

    /**
     * @var Jwt|string|array the [[Jwt]] object or the application component ID of the [[Jwt]].
     */
    public $jwt = 'jwt';

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->jwt = Instance::ensure($this->jwt, Jwt::className());
    }

    /**
     * @inheritdoc
     */
    public function parseToken($jwt)
    {
         $token = $this->loadToken($jwt);
         return $token;
    }

    /**
     * Parses the JWT and returns a token class
     * @param string $token JWT
     * @return Token|null
     */
    public function loadToken($token)
    {
        return $this->jwt->loadToken($token);
    }
}
