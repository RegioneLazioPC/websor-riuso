<?php

namespace common\models;

use Yii;

use yii\data\ActiveDataProvider;

class ViewUtentiApp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_utenti_app';
    }

    
}
