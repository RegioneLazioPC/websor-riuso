<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use backend\events\EditedUtlEventoEvent;
/**
 * This is the model class for table "richiesta_canadair".
 *
 * @property int $id
 * @property int $idevento
 * @property int $idoperatore
 * @property int $idcomunicazione
 * @property bool $engaged
 * @property string $motivo_rifiuto
 * @property string $codice_canadair
 * @property string $created_at
 *
 * @property ComComunicazioni $comunicazione
 * @property UtlEvento $evento
 * @property UtlOperatorePc $operatore
 */
class RichiestaCanadair extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'richiesta_canadair';
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
            [['idevento', 'idoperatore', 'idcomunicazione'], 'default', 'value' => null],
            [['idevento', 'idoperatore', 'idcomunicazione','edited'], 'integer'],
            [['created_at', 'codice_canadair', 'motivo_rifiuto'], 'safe'],
            ['engaged', 'boolean'],
            [['idcomunicazione'], 'exist', 'skipOnError' => true, 'targetClass' => ComComunicazioni::className(), 'targetAttribute' => ['idcomunicazione' => 'id']],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
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
            'idoperatore' => 'Idoperatore',
            'idcomunicazione' => 'Idcomunicazione',
            'engaged' => 'Ingaggiata',
            'created_at' => 'Created At',
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
