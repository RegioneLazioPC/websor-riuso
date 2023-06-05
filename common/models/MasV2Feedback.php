<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\MasInvio;
use common\models\ConMasInvioContact;

class MasV2Feedback extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mas_v2_feedback';
    }

    public function behaviors()
    {
        return [
            //TimestampBehavior::className()
        ];
    }

    protected static $map_channel = [
        'email'=>'Email',
        'pec'=>'Pec',
        'sms'=>'Sms',
        'fax'=>'Fax',
        'push android' => 'Push',
        'push ios'=>'Push'
    ];
    
    public static function channelMapped($n)
    {
        return self::$map_channel[$n];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_invio', 'status'], 'integer'],
            [['recapito','status_string','uid'], 'string'],
            [[
                'sent_date',
                'received_date',
                'refused_date',
            ], 'string'],
            [['created_at', 'updated_at'], 'integer']
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
            'recapito' => 'Recapito',
            'status' => 'Stato',
            'status_string' => 'Stato stringa',
            'sent_date' => 'Inviato il',
            'received_date' => 'Ricevuto il',
            'refused_date' => 'Rifiutato il'
        ];
    }

    public function getStato()
    {
        switch ($this->status) {
            // stato che identifica gli elementi della coda da elaborare
            case 0:
                return 'Non inviato';
                break;
            case 1:
                return 'Rifiutato';
                break;
            // feedback positivo
            case 2:
                return 'Inviato';
                break;
            // feedback negativo
            case 3:
                return 'Inviato e ricevuto';
                break;

            default:
                return 'Non inviato';
                break;
        }
    }

    public static function getStatoByNumber($n)
    {
        switch ($n) {
            // stato che identifica gli elementi della coda da elaborare
            case 0:
                return 'Non inviato';
                break;
            // stato di inizio elaborazione della coda,
            // se ci sono elementi in questo stato per un invio non ne posso fare ulteriori
            // quando arrivano feedback vengono aggiornati
            case 1:
                return 'Rifiutato';
                break;
            // feedback positivo
            case 2:
                return 'Inviato';
                break;
            // feedback negativo
            case 3:
                return 'Inviato e ricevuto';
                break;

            default:
                return 'Non inviato';
                break;
        }
    }

    public function getInvio()
    {
        return $this->hasOne(MasInvio::className(), ['id' => 'id_invio']);
    }

    public function getContatto()
    {
        return $this->hasOne(ConMasInvioContact::className(), ['id_invio' => 'id_invio', 'valore_riferimento' => 'recapito']);
    }
}
