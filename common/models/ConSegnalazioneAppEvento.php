<?php

namespace common\models;

use Yii;


class ConSegnalazioneAppEvento extends \yii\db\ActiveRecord
{
    const STATO_PENDING = 0;
    const STATO_APPROVED = 1;
    const STATO_REFUSED = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_segnalazione_app_evento';
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
            [['id_evento', 'id_segnalazione', 'confirmed'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_segnalazione' => 'Segnalazione',
            'id_evento' => 'Evento',
            'confirmed' => 'Confermata'
        ];  
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'id_evento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegnalazione()
    {
        return $this->hasOne(UtlSegnalazione::className(), ['id' => 'id_segnalazione']);
    }
}
