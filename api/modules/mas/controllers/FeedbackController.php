<?php
namespace api\modules\mas\controllers;
use Yii;

use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\InvalidRouteException;

use common\models\MasInvio;
use common\models\MasSingleSend;

use api\utils\ResponseError;
use yii\web\Response;
use yii\helpers\Url;

/**
 * Gestione del feedback dal modulo mas
 */
class FeedbackController extends Controller
{

    protected $channels = [
        'Email' => 'status_mail',
        'Pec' => 'status_pec',
        'Sms' => 'status_sms',
        'Push' => 'status_push',
        'Fax' => 'status_fax',
    ];

    private function replaceChannel( $channel ) {
        return $this->channels[$channel];
    }
    
    public function actions()
    {
        $this->enableCsrfValidation = false; 
        $actions = parent::actions();
        
        return $actions;
    }

    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors =  [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update' => ['POST']
                ],
            ]
        ];
        return $behaviors;
    }


    /**
     * Elabora feedback
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionVerify( $id, $token )
    {
        
        Yii::$app->response->format = Response::FORMAT_JSON;

        if($token != Yii::$app->params['mas_token']) ResponseError::returnSingleError(403, "Non autorizzato");
        
        $update = Yii::$app->request->post();
        // in update avrò il responso dell'invio
        $invio = MasInvio::findOne( $id );

        // delivery_attempt mi permette di gestire le duplicazioni interne allo split dei messaggi lato mas
        $delivery_attempt = !empty($update['_id_delivery']) ? $update['_id_delivery'] : null;
        // il message_id ci torna utile per il match sulle notifiche
        $message_id = !empty($update['_id']) ? $update['_id'] : null;

        //Yii::error(json_encode($update));
        //if(!$invio) 

        $rows = [];
        $now = time();


        $keys = [
            'sent',
            'received',
            'not_received',
            'not_sent',
            'invalid',
            'no_feedback',
            'duplicated',
            'mancata_consegna'
        ];

        foreach ($keys as $key) {
            $rows = [];
            if(isset($update[$key]) && count($update[$key]) > 0) {
                // per riuscire a prendere i duplicati nel caso di multipli aggiornamenti
                // ho bisogno di risalire a quei record duplicati originariamente
                // così imposto questo parametro, 1 mi identifica i duplicati
                // in questo modo quando il valore originale avrà il feedback aggiorno lo stato filtrando per questo parametro 
                // (usare lo stato = 'duplicato' mi permetterebbe un singolo aggiornamento)
                $duplicated = ($key == 'duplicated') ? 1 : 0;
                foreach ($update[$key] as $contact) {
                    if(isset($contact['ref']) ) {
                        $rows[] = [
                            $id,
                            $contact['ref']['id_rubrica_contatto'],
                            $contact['ref']['tipo_rubrica_contatto'],
                            $contact['ref']['valore_rubrica_contatto'],
                            $contact['ref']['channel'],
                            $contact['status'],
                            $contact['ref']['id'],
                            $contact['sent_time'],
                            $contact['feedback_time'],
                            $now,
                            $now,
                            !empty($contact['_id']) ? $contact['_id'] : null,
                            $delivery_attempt,
                            $duplicated,
                            $message_id
                        ]; 
                    } else {
                        $rows[] = [
                            $id,
                            $contact['id_rubrica_contatto'],
                            $contact['tipo_rubrica_contatto'],
                            $contact['valore_rubrica_contatto'],
                            $contact['channel'],
                            $contact['status'],
                            $contact['id'],
                            null,
                            null,
                            $now,
                            $now,
                            !empty($contact['_id']) ? $contact['_id'] : null,
                            $delivery_attempt,
                            $duplicated,
                            $message_id
                        ]; 
                    }
                }

                if( count($rows) > 0 ) {
                    $conn = Yii::$app->db;
                    $dbTrans = $conn->beginTransaction();

                    try {
                        $insert_command = Yii::$app->db->createCommand()
                        ->batchInsert(
                            MasSingleSend::tableName(), 
                            [
                                'id_invio',
                                'id_rubrica_contatto',
                                'tipo_rubrica_contatto',
                                'valore_rubrica_contatto',
                                'channel',
                                'status',
                                'id_con_mas_invio_contact',
                                'sent_time',
                                'feedback_time',
                                'created_at',
                                'updated_at',
                                'id_feedback',
                                '_id_delivery',
                                'duplicated_record',
                                'id_mas_message'
                            ], 
                            $rows
                        );
                        /**
                         * Mantengo l'id feedback per futuri aggiornamenti
                         * il constrain sulla chiave id_feedback mi permette di aggiornare quando invece di un inserimento devo fare un update
                         */
                        $sql = $insert_command->getRawSql();
                        $command = Yii::$app->db->createCommand( $sql . " 
                        ON CONFLICT (id_feedback) 
                        DO
                         UPDATE
                           SET 
                           status = EXCLUDED.status,
                           sent_time = EXCLUDED.sent_time,
                           feedback_time = EXCLUDED.feedback_time,
                           updated_at = EXCLUDED.updated_at
                        ");
                        
                        //Yii::error( $command->getRawSql() );

                        $command->execute();
                        $dbTrans->commit();

                    } catch(\Exception $e) {
                        $dbTrans->rollback();
                        throw $e;
                    }

                }

            }

        }

        $conn = Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        try {

            // Con questa query andiamo ad aggiornare tutti i duplicati con i valore del record utilizzato per il delivery    
            Yii::$app->db->createCommand("
                UPDATE mas_single_send AS duplicates
                SET 
                \"status\" = o.\"status\",
                sent_time = o.sent_time,
                feedback_time = o.feedback_time,
                updated_at = o.updated_at,
                id_mas_message = o.id_mas_message
                FROM mas_single_send o
                WHERE 
                o.duplicated_record = 0 AND 
                duplicates.valore_rubrica_contatto = o.valore_rubrica_contatto AND
                duplicates.duplicated_record = 1 AND 
                duplicates._id_delivery = :delivery AND
                o._id_delivery = :delivery
            ")
            ->bindParam(':delivery',$delivery_attempt)
            ->execute();
            
            $dbTrans->commit();

        } catch(\Exception $e) {
            $dbTrans->rollback();
        }

    }   


    private function setInvioStatus( $invio ) {
        $enable = true;
        /**
         * Non rendere disponibile l'invio se non sono liberi tutti i canali
         * @var [type]
         */
        foreach ($this->channels as $key => $value) {
            if($invio->$value == 1) $enable = false;
        }

        /**
         * Sono tutti liberi, riabilita l'invio
         */
        if($enable) {
            $invio->status = 0;
            $invio->save();
        }
    }

}