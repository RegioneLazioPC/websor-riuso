<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

use common\models\ConMezzoSchieramento;
use common\models\ConAttrezzaturaSchieramento;
use common\models\UtlAutomezzo;
use common\models\UtlAttrezzatura;

class VolSchieramento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_schieramento';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ],
            TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
            [['descrizione'], 'required'],
            [['descrizione'], 'string'],
            [['data_validita'], 'safe']
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
            'data_validita' => 'Data validità',
            'created_at' => 'Creazione',
            'updated_at' => 'Ultimo aggiornamento'
        ];
    }

    public function extraFields() {
        return ['conMezzi', 'conAttrezzature', 'mezzi', 'attrezzature'];
    }

    public function getConMezzi() {
        return $this->hasMany(ConMezzoSchieramento::className(), ['id_vol_schieramento'=>'id']);
    }

    public function getConAttrezzature() {
        return $this->hasMany(ConAttrezzaturaSchieramento::className(), ['id_vol_schieramento'=>'id']);
    }

    public function getMezzi() {
        return $this->hasMany(UtlAutomezzo::className(), ['id'=>'id_utl_automezzo'])
        ->via('conMezzi');
    }

    public function getAttrezzature() {
        return $this->hasMany(UtlAttrezzatura::className(), ['id'=>'id_utl_attrezzatura'])
        ->via('conAttrezzature');
    }

    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave($insert, $changedAttributes);
    }

}
