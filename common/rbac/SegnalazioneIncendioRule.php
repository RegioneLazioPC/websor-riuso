<?php

namespace common\rbac;

use common\models\UtlOperatorePc;
use yii\rbac\Rule;


/**
 * Verifica che la tipologia corrisponda all'id dell'incendio
 * @deprecated
 */
class SegnalazioneIncendioRule extends Rule
{
    public $name = 'segnalazioneIncendio';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        
        return isset($params['tipologia']) ? $params['tipologia'] == 1 : false;
    }
}