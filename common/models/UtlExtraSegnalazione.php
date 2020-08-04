<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_extra_segnalazione".
 *
 * @property integer $id
 * @property string $voce
 * @property integer $parent_id
 *
 * @property ConSegnalazioneExtra[] $conSegnalazioneExtras
 */
class UtlExtraSegnalazione extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_extra_segnalazione';
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
            [['parent_id','order'], 'integer'],
            [['show_numero','show_note', 'show_num_nuclei_familiari', 'show_num_disabili'], 'safe'],
            [['show_num_sistemazione_parenti_amici', 'show_num_sistemazione_strutture_ricettive', 'show_num_sistemazione_area_ricovero', 'show_num_persone_isolate', 'show_num_utenze'], 'safe'],
            [['voce'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'voce' => 'Voce',
            'parent_id' => 'Parent ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConSegnalazioneExtras()
    {
        return $this->hasMany(ConSegnalazioneExtra::className(), ['idextra' => 'id']);
    }

    public function getChildren()
    {
        return $this->hasMany(UtlExtraSegnalazione::className(), ['parent_id'=>'id']);
    }
}
