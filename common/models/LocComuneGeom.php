<?php

namespace common\models;

use Yii;

use yii\data\ActiveDataProvider;


class LocComuneGeom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loc_comune_geom';
    }


}
