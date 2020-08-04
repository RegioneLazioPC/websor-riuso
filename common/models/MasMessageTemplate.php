<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mass_message_template".
 *
 * @property int $id
 * @property string $nome
 * @property string $mail_body
 * @property string $sms_body
 * @property string $push_body
 * @property string $fax_body
 * @property int $created_at
 * @property int $updated_at
 *
 * @property MasMessage[] $massMessages
 */
class MasMessageTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mas_message_template';
    }

    /**
     * @inheritdoc
     */
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
            [['mail_body'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['nome', 'push_body', 'fax_body'], 'string', 'max' => 255],
            [['sms_body'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'mail_body' => 'Mail/Pec',
            'sms_body' => 'Sms',
            'push_body' => 'Push Notifications',
            'fax_body' => 'Fax',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasMessages()
    {
        return $this->hasMany(MasMessage::className(), ['id_template' => 'id']);
    }
}
