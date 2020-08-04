<?php

namespace common\models;

use common\models\LocIndirizzo;
use Yii;

/**
 * This is the model class for table "loc_civico".
 *
 */
class LocCivico extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loc_civico';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_indirizzo', 'civico', 'lat', 'lon'], 'required'],
            [['id_indirizzo'], 'integer'],
            [['civico', 'cap'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_indirizzo' => 'Indirizzo',
            'civico' => 'N.',
            'lat' => 'Latitudine',
            'lon' => 'Longitudine',
            'cap' => 'Cap'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIndirizzo()
    {
        return $this->hasOne(LocIndirizzo::className(), ['id' => 'id_indirizzo']);
    }

}
