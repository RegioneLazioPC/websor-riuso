<?php

namespace common\models\cartografia;

use Yii;

class ViewCartografiaAutomezzo extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_cartografia_automezzi';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id"];
    }

}
