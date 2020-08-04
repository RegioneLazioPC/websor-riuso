<?php

namespace common\models\cartografia;

use Yii;

class ViewCartografiaSegnalazione extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_cartografia_segnalazioni';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id"];
    }

}
