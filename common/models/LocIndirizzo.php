<?php

namespace common\models;

use common\models\LocComune;
use Yii;

/**
 * This is the model class for table "loc_indirizzo".
 *
 */
class LocIndirizzo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loc_indirizzo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_comune', 'name'], 'required'],
            [['id_comune'], 'integer'],
            [['name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_comune' => 'Comune',
            'name' => 'Via',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'id_comune']);
    }

}
