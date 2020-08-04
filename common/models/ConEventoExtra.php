<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "con_evento_extra".
 *
 * @property integer $id
 * @property integer $idevento
 * @property integer $idextra
 * @property integer $numero
 * @property string $note
 * @property integer $numero_nuclei_familiari
 * @property integer $numero_disabili
 * @property integer $numero_sistemazione_parenti_amici
 * @property integer $numero_sistemazione_strutture_ricettive
 * @property integer $numero_sistemazione_area_ricovero
 * @property integer $numero_persone_isolate
 * @property integer $numero_utenze
 *
 * @property UtlExtraSegnalazione $idextra0
 * @property UtlSegnalazione $idevento0
 */
class ConEventoExtra extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_evento_extra';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevento', 'idextra', 'numero', 'numero_nuclei_familiari', 'numero_disabili', 'numero_sistemazione_parenti_amici', 'numero_sistemazione_strutture_ricettive', 'numero_sistemazione_area_ricovero', 'numero_persone_isolate', 'numero_utenze'], 'integer'],
            [['note'], 'string', 'max' => 255],
            [['idextra'], 'exist', 'skipOnError' => true, 'targetClass' => UtlExtraSegnalazione::className(), 'targetAttribute' => ['idextra' => 'id']],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idevento' => 'Idevento',
            'idextra' => 'Idextra',
            'numero' => 'Numero',
            'note' => 'Note',
            'numero_nuclei_familiari' => 'Numero Nuclei Familiari',
            'numero_disabili' => 'Numero Disabili',
            'numero_sistemazione_parenti_amici' => 'Numero Sistemazione Parenti Amici',
            'numero_sistemazione_strutture_ricettive' => 'Numero Sistemazione Strutture Ricettive',
            'numero_sistemazione_area_ricovero' => 'Numero Sistemazione Area Ricovero',
            'numero_persone_isolate' => 'Numero Persone Isolate',
            'numero_utenze' => 'Numero Utenze',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @deprecated
     */
    public function getIdextra0()
    {
        return $this->hasOne(UtlExtraSegnalazione::className(), ['id' => 'idextra']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @deprecated
     */
    public function getIdevento0()
    {
        return $this->hasOne(UtlSegnalazione::className(), ['id' => 'idevento']);
    }
}
