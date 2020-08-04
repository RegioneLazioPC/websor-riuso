<?php

namespace api\utils;

use sizeg\jwt\JwtHttpBearerAuth;
use yii\web\UnauthorizedHttpException;

/**
 * La magia di yii2 e dei pacchetti relativi ci costringe a questo per la modifica di un semplice messaggio, ok
 * 
 * handleFailure è un metodo della classe estesa da JwtHttpBearerAuth
 * qui lo riscriviamo solo per cambiare il messaggio di errore che è in inglese non traducibile
 *
 * @author Fabio Rizzo
 */
class Authenticator extends JwtHttpBearerAuth
{
    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Credenziali non valide');
    }

}
