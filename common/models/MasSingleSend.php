<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\MasInvio;
/**
 * This is the model class for table "mas_single_send".
 *
 * @property int $id
 * @property int $id_invio
 * @property int $id_rubrica_contatto
 * @property int $tipo_rubrica_contatto
 * @property int $channel
 * @property int $status
 * @property int $sending_attempts
 * @property string $last_attempt
 */
class MasSingleSend extends \yii\db\ActiveRecord
{
    const STATUS_DUPLICATED = -1;
    const STATUS_ADDED = 0;
    const STATUS_READY = 1;
    const STATUS_SENT = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_REFUSED = 4;
    const INVALID_CONTACT = 5;
    const STATUS_NOT_SENT = 6;
    const STATUS_NO_FEEDBACK = 7;
    const STATUS_TO_VERIFY_IMAP = 8;
    const STATUS_MANCATA_CONSEGNA_IMAP = 9;
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mas_single_send';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_invio', 'id_rubrica_contatto', 'tipo_rubrica_contatto', 'valore_rubrica_contatto', 'channel', 'status'], 'default', 'value' => null],
            [['id_feedback_v2'], 'string'],
            [['id_invio', 'id_rubrica_contatto', 'status','id_con_mas_invio_contact', 'id_message'], 'integer'],
            [['last_attempt','sent_time','feedback_time'], 'safe'],
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
            'status' => 'Status',
            'sending_attempts' => 'Sending Attempts',
            'last_attempt' => 'Last Attempt',
            'id_con_mas_invio_contact' => 'Contatto'
        ];
    }

    public function getStato() {
        switch ($this->status) {
            // stato che identifica gli elementi della coda da elaborare
            case 0:
                return 'Non inviato';
                break;
            // stato di inizio elaborazione della coda,
            // se ci sono elementi in questo stato per un invio non ne posso fare ulteriori
            // quando arrivano feedback vengono aggiornati
            case 1:
                return 'Invio in corso';
                break;
            // feedback positivo
            case 2:
                return 'Pending';
                break;
            // feedback negativo
            case 3:
                return 'Inviato e ricevuto';
                break;  

            case 4:
                return 'Inviato ma non ricevuto';
                break;            
            
            default:
                return 'Non inviato';
                break;
        }
    }

    //const STATUS_DUPLICATED = -1;
    //const STATUS_ADDED = 0;
    //const STATUS_READY = 1;
    //const STATUS_SENT = 2;
    //const STATUS_RECEIVED = 3;
    //const STATUS_REFUSED = 4;
    //const INVALID_CONTACT = 5;
    //const STATUS_NOT_SENT = 6;
    //const STATUS_NO_FEEDBACK = 7;
    public static function getStatoByNumber( $n ) {
        switch ($n) {
            // stato che identifica gli elementi della coda da elaborare
            case -1:
                return 'Contatto duplicato';
                break;
            case 0:
                return 'Non inviato';
                break;
            // stato di inizio elaborazione della coda,
            // se ci sono elementi in questo stato per un invio non ne posso fare ulteriori
            // quando arrivano feedback vengono aggiornati
            case 1:
                return 'Invio in corso';
                break;
            // feedback positivo
            case 2:
                return 'Inviato';
                break;
            // feedback negativo
            case 3:
                return 'Inviato e ricevuto';
                break;  

            case 4:
                return 'Rifiutato';
                break;
            case 5:
                return 'Contatto non valido';
                break;            
            case 6:
                return 'Non inviato';
                break;            
            case 7:
                return 'Nessun feedback disponibile';
                break;            
            case 8:
                return 'Imap da verificare';
                break; 
            case 9:
                return 'Mancata consegna';
                break;   
            
            default:
                return 'Non inviato';
                break;
        }
    }

    public static function getStatoByNumberV2( $n ) {
        switch ($n) {
            // stato che identifica gli elementi della coda da elaborare
            case -1:
                return 'Contatto duplicato';
                break;
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

            case 4:
                return 'Rifiutato';
                break;
            case 5:
                return 'Contatto non valido';
                break;            
            case 6:
                return 'Non inviato';
                break;            
            case 7:
                return 'Nessun feedback disponibile';
                break;            
            case 8:
                return 'Imap da verificare';
                break; 
            case 9:
                return 'Mancata consegna';
                break;   
            
            default:
                return 'Non inviato';
                break;
        }
    }

    public function getInvio() {
        return $this->hasOne(MasInvio::className(), ['id' => 'id_invio']);
    }

    public static function calculateStats( $id_invio ) {
        try {
            
            $invio = MasInvio::findOne( $id_invio );
            
            $total = \common\models\MasSingleSend::find()
                ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM con_mas_invio_contact)'])->where(['id_invio'=>$id_invio])->count();


            $contattati = \common\models\MasSingleSend::find()
                ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM con_mas_invio_contact)'])->where(['id_invio'=>$id_invio])->count();



            if(!empty($invio->mas_ref_id)) {        
                
                $result = Yii::$app->db->createCommand("SELECT count( distinct (id_rubrica_contatto, tipo_rubrica_contatto) ) as num_recapitati FROM con_mas_invio_contact WHERE valore_rubrica_contatto in (
                    SELECT distinct recapito as rec
                    FROM mas_v2_feedback m
                    WHERE m.id_invio = :id_invio
                    AND ( (\"status\" in (2,3) AND m.channel not in ('Pec','Fax','Sms')) OR (\"status\" = 3 and m.channel in ('Pec','Fax','Sms')))
                    )
                    AND id_invio = :id_invio", [':id_invio' => $id_invio]
                )
                ->queryAll();
                $delivered = isset($result[0]) ? $result[0]['num_recapitati'] : 0;
                
                
            } else {


                $connection = Yii::$app->getDb();
                $command = $connection->createCommand("WITH t as (SELECT count(\"status\") FILTER (
                        WHERE 
                            (\"status\" in (:sent,:received) AND channel not in ('Pec','Fax','Sms')) OR 
                            (\"status\" = :received and channel in ('Pec','Fax','Sms'))
                            ) > 0 as delivered 
                    FROM mas_single_send m WHERE id_invio = :id_invio
                    GROUP BY tipo_rubrica_contatto, id_rubrica_contatto)
                    SELECT count(delivered) FILTER (WHERE delivered is true) as delivered FROM t;
                        ;", [
                    ':id_invio' => intval($id_invio),
                    ':sent' => \common\models\MasMessage::STATUS_SEND[0],
                    ':received' => \common\models\MasMessage::STATUS_RECEIVED[0]
                ]);

                $result = $command->queryAll();
                $delivered = $result[0]['delivered'];

            }  

            return [
                'total' => $total,
                'contacted' => $contattati,
                'delivered' => $delivered,
                'not_delivered' => $contattati - $delivered
            ];
        } catch( \Exception $e ) {
            
            Yii::error($e->getMessage());
            return [
                'total' => 0,
                'contacted' => 0,
                'delivered' => 0,
                'not_delivered' => 0
            ];
        }
    }
}
