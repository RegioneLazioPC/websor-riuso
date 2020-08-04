<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loc_regione".
 *
 * @property integer $id
 * @property string $regione
 */
class LocRegione extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loc_regione';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'regione'], 'required'],
            [['id'], 'integer'],
            [['regione'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'regione' => 'Regione',
        ];
    }
}
