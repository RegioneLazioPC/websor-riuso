<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "con_segnalazione_extra".
 *
 * @property integer $idsegnalazione
 * @property integer $idextra
 *
 * @property UtlExtraSegnalazione $idextra0
 * @property UtlSegnalazione $idsegnalazione0
 */
class ConSegnalazioneExtra extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_segnalazione_extra';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idsegnalazione', 'idextra', 'numero', 'numero_nuclei_familiari'], 'integer'],
            [['numero_disabili', 'numero_sistemazione_parenti_amici', 'numero_sistemazione_strutture_ricettive', 'numero_sistemazione_area_ricovero', 'numero_persone_isolate', 'numero_utenze'], 'integer'],
            [['note'], 'string'],
            [['idextra'], 'exist', 'skipOnError' => true, 'targetClass' => UtlExtraSegnalazione::className(), 'targetAttribute' => ['idextra' => 'id']],
            [['idsegnalazione'], 'exist', 'skipOnError' => true, 'targetClass' => UtlSegnalazione::className(), 'targetAttribute' => ['idsegnalazione' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idsegnalazione' => 'Idsegnalazione',
            'idextra' => 'Idextra',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdextra()
    {
        return $this->hasOne(UtlExtraSegnalazione::className(), ['id' => 'idextra']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdsegnalazione()
    {
        return $this->hasOne(UtlSegnalazione::className(), ['id' => 'idsegnalazione']);
    }
}
