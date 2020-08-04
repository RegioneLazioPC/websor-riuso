<?php

namespace common\models\external;

use Yii;

/**
 * This is the model class for table "GpsRadio".
 *
 * Collegamento a db esterno con posizione delle radio mobili
 *
 * @property integer $id
 * @property integer $id_regione
 * @property string $provincia
 * @property string $sigla
 */
class RadioMobili extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'GpsRadio';
    }

    public static function getDb() {
        return Yii::$app->dbsqlserver;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }
}
