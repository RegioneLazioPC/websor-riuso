<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

use backend\events\EditedUtlEventoEvent;
/**
 * This is the model class for table "richiesta_dos".
 *
 * @property int $id
 * @property int $idevento
 * @property int $idingaggio
 * @property int $idoperatore
 * @property int $idcomunicazione
 * @property bool $engaged
 * @property string $motivo_rifiuto
 * @property string $codicedos
 * @property string $created_at
 *
 * @property ComComunicazioni $comunicazione
 * @property UtlEvento $evento
 * @property UtlIngaggio $ingaggio
 * @property UtlOperatorePc $operatore
 */
class RichiestaDos extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'richiesta_dos';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            // Other behaviors
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
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
            [['idevento', 'idingaggio', 'idoperatore', 'idcomunicazione'], 'default', 'value' => null],
            [['idevento', 'idingaggio', 'idoperatore', 'idcomunicazione', 'edited'], 'integer'],
            //[['created_at'], 'required'],
            [['created_at', 'codicedos', 'motivo_rifiuto'], 'safe'],
            ['engaged', 'boolean'],
            [['idcomunicazione'], 'exist', 'skipOnError' => true, 'targetClass' => ComComunicazioni::className(), 'targetAttribute' => ['idcomunicazione' => 'id']],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
            [['idingaggio'], 'exist', 'skipOnError' => true, 'targetClass' => UtlIngaggio::className(), 'targetAttribute' => ['idingaggio' => 'id']],
            [['idoperatore'], 'exist', 'skipOnError' => true, 'targetClass' => UtlOperatorePc::className(), 'targetAttribute' => ['idoperatore' => 'id']],
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
            'idingaggio' => 'Idingaggio',
            'idoperatore' => 'Idoperatore',
            'idcomunicazione' => 'Idcomunicazione',
            'created_at' => 'Created At',
            'motivo_rifiuto' => 'Note',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComunicazione()
    {
        return $this->hasOne(ComComunicazioni::className(), ['id' => 'idcomunicazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngaggio()
    {
        return $this->hasOne(UtlIngaggio::className(), ['id' => 'idingaggio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatore()
    {
        return $this->hasOne(UtlOperatorePc::className(), ['id' => 'idoperatore']);
    }

    public function afterSave($insert, $changedAttributes) 
    {
        parent::afterSave($insert, $changedAttributes);
        
        EditedUtlEventoEvent::handleEdited($this->idevento);
    }

}
