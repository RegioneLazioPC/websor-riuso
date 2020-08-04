<?php

namespace common\models\cartografia;

use Yii;

class ViewCartografiaAttrezzatura extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_cartografia_attrezzature';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id"];
    }

}
