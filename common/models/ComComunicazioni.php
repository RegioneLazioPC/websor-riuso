<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "com_comunicazioni".
 *
 * @property int $id
 * @property int $tipo
 * @property string $oggetto
 * @property string $contenuto
 * @property string $contatto
 * @property string $created_at
 *
 * @property RichiestaDos[] $richiestaDos
 */
class ComComunicazioni extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'com_comunicazioni';
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
            [['tipo'], 'default', 'value' => null],
            [['tipo'], 'integer'],
            [['contenuto'], 'string'],
            //[['created_at'], 'required'],
            [['created_at'], 'safe'],
            [['oggetto', 'contatto'], 'string', 'max' => 255],
            [['contatto'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'oggetto' => 'Oggetto',
            'contenuto' => 'Contenuto',
            'contatto' => 'Contatto',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRichiestaDos()
    {
        return $this->hasMany(RichiestaDos::className(), ['idcomunicazione' => 'id']);
    }
}
