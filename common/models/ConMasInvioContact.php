<?php

namespace common\models;

use Yii;

use common\models\MasSingleSend;
/**
 * This is the model class for table "con_mas_invio_contact".
 *
 * @property int $id
 * @property int $id_invio
 * @property int $id_rubrica_contatto
 * @property int $tipo_rubrica_contatto
 * @property string $channel
 * @property string $valore_rubrica_contatto
 *
 * @property MasSingleSend[] $masSingleSends
 *
 * @deprecated
 */
class ConMasInvioContact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_mas_invio_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_invio', 'id_rubrica_contatto', 'tipo_rubrica_contatto'], 'default', 'value' => null],
            [['id_invio', 'id_rubrica_contatto'], 'integer'],
            [['channel'], 'string', 'max' => 20],
            [['ext_id', 'everbridge_identifier'], 'string'],
            [['valore_riferimento'], 'string'],
            [['valore_rubrica_contatto'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_invio' => 'Id Invio',
            'id_rubrica_contatto' => 'Id Rubrica Contatto',
            'tipo_rubrica_contatto' => 'Tipo Rubrica Contatto',
            'channel' => 'Channel',
            'valore_rubrica_contatto' => 'Valore Rubrica Contatto',
        ];
    }

    public static function getCanali() {
        return [
            'Email'=>'Email',
            'Pec'=>'Pec',
            'Fax'=>'Fax',
            'Sms'=>'Sms',
            'Push'=>'Push',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasSingleSends()
    {
        return $this->hasMany(MasSingleSend::className(), [
            'id_con_mas_invio_contact' => 'id'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasSingleSendsWithDuplicates()
    {
        return $this->hasMany(MasSingleSend::className(), [
            'valore_rubrica_contatto' => 'valore_rubrica_contatto',
            'channel' => 'channel',
            'id_invio' => 'id_invio'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasSingleSendsWithoutDuplicates()
    {
        return $this->hasMany(MasSingleSend::className(), [
            'valore_rubrica_contatto' => 'valore_rubrica_contatto',
            'id_rubrica_contatto' => 'id_rubrica_contatto',
            'tipo_rubrica_contatto' => 'tipo_rubrica_contatto',
            'channel' => 'channel',
            'id_invio' => 'id_invio'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasSingleSendsAggregated()
    {
        return $this->hasMany(MasSingleSend::className(), [
            'id_rubrica_contatto' => 'id_rubrica_contatto',
            'tipo_rubrica_contatto' => 'tipo_rubrica_contatto',
            'id_invio' => 'id_invio'
        ]);
    }

    public function getContatto()
    {
        return $this->hasOne(ViewRubrica::className(), [
            'id_riferimento'=>'id_rubrica_contatto', 
            'tipo_riferimento'=>'tipo_rubrica_contatto',
            'valore_contatto' => 'valore_rubrica_contatto'
        ]);
    }
}
