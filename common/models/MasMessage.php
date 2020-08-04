<?php

namespace common\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\UplMedia;

/**
 * This is the model class for table "mass_message".
 *
 * @property int $id
 * @property int $id_template
 * @property string $note
 * @property int $channel_mail
 * @property int $channel_pec
 * @property int $channel_push
 * @property int $channel_sms
 * @property int $channel_fax
 * @property string $mail_text
 * @property string $sms_text
 * @property string $push_text
 * @property string $fax_text
 * @property int $created_at
 * @property int $updated_at
 *
 * @property MasMessageTemplate $template
 */
class MasMessage extends \yii\db\ActiveRecord
{
    const STATUS_SEND = [2,3];
    const STATUS_RECEIVED = [3];

    public $mediaFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mas_message';
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
            [['id_template', 'channel_mail', 'channel_pec', 'channel_push', 'channel_sms', 'channel_fax', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['id_template', 'channel_mail', 'channel_pec', 'channel_push', 'channel_sms', 'channel_fax', 'created_at', 'updated_at', 'id_media'], 'integer'],
            [['mail_text','fax_text'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'required'],
            [['note', 'push_text'], 'string', 'max' => 255],
            [['sms_text'], 'string', 'max' => 255],
            [['id_template'], 'exist', 'skipOnError' => true, 'targetClass' => MasMessageTemplate::className(), 'targetAttribute' => ['id_template' => 'id']],
            [['id_allerta'], 'exist', 'skipOnError' => true, 'targetClass' => AlmAllertaMeteo::className(), 'targetAttribute' => ['id_allerta' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_template' => 'Id Template',
            'title' => 'Titolo',
            'note' => 'Note',
            'channel_mail' => 'Channel Mail',
            'channel_pec' => 'Channel Pec',
            'channel_push' => 'Channel Push',
            'channel_sms' => 'Channel Sms',
            'channel_fax' => 'Channel Fax',
            'mail_text' => 'Mail Text',
            'sms_text' => 'Sms Text',
            'push_text' => 'Push Text',
            'fax_text' => 'Fax Text',
            'id_user' => 'Utente',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function validMessageMimes() {
        return [
            'application/pdf',
            'application/msword',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-word.template.macroEnabled.12',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.ms-office'
            ];
    }

    public static function validAllertaMimes() {
        return [
            'application/pdf',
            'application/msword',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-word.template.macroEnabled.12',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.ms-office'
            ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(MasMessageTemplate::className(), ['id' => 'id_template']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllerta()
    {
        return $this->hasOne(AlmAllertaMeteo::className(), ['id' => 'id_allerta']);
    }

    public function getInvio() 
    {
        return $this->hasMany(MasInvio::className(), ['id_message'=>'id']);
    }

    public function getUser() 
    {
        return $this->hasOne(User::className(), ['id'=>'id_user']);
    }
   
    public function getFile()
    {
        return $this->hasMany(UplMedia::className(), ['id' => 'id_media'])
        ->viaTable('con_mas_message_media', ['id_mas_message'=>'id']);
    }

    public function beforeSave($insert)
    {
        if($insert) $this->id_user = @Yii::$app->user->identity->id;
        
        return parent::beforeSave($insert);
    }
}
