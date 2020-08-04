<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "tbl_sezione_specialistica".
 *
 * @property int $id
 * @property int $descrizione
 *
 * @property VolSede[] $volSedes
 */
class TblSezioneSpecialistica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_sezione_specialistica';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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

    
}
