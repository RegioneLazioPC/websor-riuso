<?php

namespace common\rbac;

use common\models\UtlOperatorePc;
use yii\rbac\Rule;


/**
 * Checks if authorID matches user passed via params
 * @deprecated
 */
class SopHasProvinciaRule extends Rule
{
    public $name = 'sopHasProvincia';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $operatore = UtlOperatorePc::find()->where(['iduser' => $user])->one();
        
        return isset($params['provincia']) ? $params['provincia'] == $operatore->salaoperativa->sigla_provincia : false;
    }
}