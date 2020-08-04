<?php

namespace common\models;

use Yii;
/**
 * This is the model class for table "utl_specializzazione".
 *
 * @property int $id
 * @property int $descrizione
 *
 * @property VolSede[] $volSedes
 */
class UtlSpecializzazione extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_specializzazione';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descrizione'], 'required'],
            [['descrizione', 'id_sync'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descrizione' => 'Descrizione',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolSedes()
    {
        return $this->hasMany(VolSede::className(), ['id_specializzazione' => 'id']);
    }
}
