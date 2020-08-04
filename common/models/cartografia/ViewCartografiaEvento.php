<?php

namespace common\models\cartografia;

use Yii;

class ViewCartografiaEvento extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_cartografia_eventi';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id"];
    }

}
