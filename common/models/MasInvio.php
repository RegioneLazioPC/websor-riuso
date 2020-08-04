<?php

namespace common\models;

use Yii;
use common\models\ConMasInvioContact;
use common\models\User;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mas_invio".
 *
 * @property int $id
 * @property int $id_message
 * @property string $data_invio
 * @property int $channel_mail
 * @property int $channel_pec
 * @property int $channel_push
 * @property int $channel_sms
 * @property int $channel_fax
 */
class MasInvio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mas_invio';
    }

    public function behaviors()
    {
        return [
            // Other behaviors
            [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_message', 'channel_mail', 'channel_pec', 'channel_push', 'channel_sms', 'channel_fax'], 'default', 'value' => null],
            [['id_message', 'channel_mail', 'channel_pec', 'channel_push', 'channel_sms', 'channel_fax',], 'integer'],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
            [['data_invio'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_message' => 'Id Message',
            'data_invio' => 'Data Invio',
            'channel_mail' => 'Channel Mail',
            'channel_pec' => 'Channel Pec',
            'channel_push' => 'Channel Push',
            'channel_sms' => 'Channel Sms',
            'channel_fax' => 'Channel Fax',
            'id_user' => 'Utente',
        ];
    }

    public function getMessage() {
        return $this->hasOne(MasMessage::className(), ['id'=>'id_message']);
    }

    public function getContatto() {
        return $this->hasMany(ViewRubrica::className(), [
            'id_riferimento'=>'id_rubrica_contatto', 
            'tipo_riferimento'=>'tipo_rubrica_contatto',
            'valore_contatto' => 'valore_rubrica_contatto'])
        ->viaTable('con_mas_invio_contact', ['id_invio'=>'id']);
    }

    public function getConInvioContatto() {
        return $this->hasMany(ConMasInvioContact::className(), ['id_invio'=>'id']);
    }

    public function getSingleSend() {
        return $this->hasMany(MasSingleSend::className(), [
            'id_rubrica_contatto'=>'id_rubrica_contatto',
            'tipo_rubrica_contatto'=>'tipo_rubrica_contatto',
            'valore_rubrica_contatto'=>'valore_rubrica_contatto'
        ])
        ->viaTable('con_mas_invio_contact', ['id_invio'=>'id']);
    }

    public function isFree() {
        $response = \common\utils\MasDispatcher::getInvioStatus( $this->id );
        $response = json_decode($response, true);
        return (isset($response['data']) && count($response['data']) == 0) ? true : false;
        
    }

    public function getUser() 
    {
        return $this->hasOne(User::className(), ['id'=>'id_user']);
    }

    public function beforeSave($insert)
    {
        if($insert) $this->id_user = @Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }

}
