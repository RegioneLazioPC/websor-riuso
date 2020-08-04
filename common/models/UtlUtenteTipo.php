<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_utente_tipo".
 *
 * @property int $id
 * @property string $descrizione
 *
 * @property VolOrganizzazioneUtente[] $volOrganizzazioneUtentes
 */
class UtlUtenteTipo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_utente_tipo';
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
            [['descrizione'], 'string', 'max' => 300],
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
    public function getVolOrganizzazioneUtentes()
    {
        return $this->hasMany(VolOrganizzazioneUtente::className(), ['idtipo' => 'id']);
    }

}
