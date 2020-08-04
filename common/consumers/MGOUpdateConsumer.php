<?php

namespace common\consumers;

use Yii;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

use common\components\Syncer;

/**
 * 
 * Tutte le sincronizzazioni utilizzano un id_sync con prefisso MGO_, 
 * essendo questo il consumer della coda MGO è giusto utilizzare quel prefisso,
 * per future integrazioni il prefisso identificherà univocamente l'owner del dato
 * 
 * Fallback in caso di errore della sincronizzazione http con MGO in entrata
 * 
 */
class MGOUpdateConsumer implements ConsumerInterface
{
    public $message;

    private $actions = [
        'updated_organizzazione',
        'created_organizzazione',
        'deleted_organizzazione',
        'updated_ente',
        'created_ente',
        'deleted_ente',
        'updated_struttura',
        'created_struttura',
        'deleted_struttura',

        'updated_risorsa',
        'created_risorsa',
        'deleted_risorsa',

        'updated_sede',
        'created_sede',
        'deleted_sede',
        'updated_ente_sede',
        'created_ente_sede',
        'deleted_ente_sede',
        'updated_struttura_sede',
        'created_struttura_sede',
        'deleted_struttura_sede',


        'updated_volontario',
        'created_volontario',
        'deleted_volontario',
        'updated_anagraficavolontario',
        'updated_anagrafica',
        'created_anagrafica',
        'deleted_anagrafica',
        'updated_tipoOrganizzazione',
        'created_tipoOrganizzazione',
        'deleted_tipoOrganizzazione',
        'updated_tipoRisorsa',
        'created_tipoRisorsa',
        'deleted_tipoRisorsa',
        'updated_tipoRisorsaMeta',
        'created_tipoRisorsaMeta',
        'deleted_tipoRisorsaMeta',

        'updated_specializzazione',
        'created_specializzazione',
        'deleted_specializzazione',

        'updated_sezioneSpecialistica',
        'created_sezioneSpecialistica',
        'deleted_sezioneSpecialistica',

        'updated_ruoloVolontario',
        'created_ruoloVolontario',
        'deleted_ruoloVolontario',
        
        'deleted_indirizzo',
        'deleted_contatto'
    ];

    /**
     * @param AMQPMessage $msg
     * ./yii rabbitmq/consume WEBSOR_LISTEN_FROM_MGO
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        Yii::info("Sync with consumer: ".$msg->body, 'sync');
        $this->message = unserialize($msg->body);
        echo "Ricevuti dati da MGO\n";
        // Apply your business logic here
        
        try {
            if(in_array($this->message['action'], $this->actions)) :
                $this->parseMessage();
            else:
                throw new \Exception("Action non valida", 1);                
            endif;
        } catch (\Exception $e) {
            Yii::info("Errore sync consumer: ".$e->getMessage(), 'sync');
            throw $e;
        } catch (\Throwable $e) {
            Yii::info("Errore sync consumer: ".$e->getMessage(), 'sync');
            throw $e;
        }

        return ConsumerInterface::MSG_ACK;
    }

    /**
     * Elabora la richiesta di aggiornamento
     * @return [type] [description]
     */
    private function parseMessage() 
    {
        $syncer = new Syncer($this->message);
        call_user_func( array( $syncer, $this->message['action'] ) );
    }

    


}

