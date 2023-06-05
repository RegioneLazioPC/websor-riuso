<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `cap_exposed_message`.
 */
class m210429_151120_create_cap_exposed_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cap_exposed_message', [
            'id' => $this->primaryKey(),
            'message_progr' => $this->integer(), // progressivo messaggio evento
            'id_evento' => $this->integer(),
            'identifier' => $this->string(),
            'sender' => $this->string(), // precompilato da params
            'sent' => 'TIMESTAMP WITH TIME ZONE',
            'status' => $this->string(), // Actual, Exercise (in note viene specificato), System, Test, Draft (non usare)
            'msgType' => $this->string(), // Alert, Update (dovrebbe sostutuire il precedente in references, ma non è così), Cancel (Cancella il precedente in references), Ack (non usare), Error (rigetto del precedente) 
            'source' => $this->string(), // da params, il segnalatore lo abbiamo solo nelle segnalazioni
            'scope' => $this->string(), // Public, Restricted, Private
            'restriction' => $this->string(), // non lo usiamo
            'addresses' => $this->string(), // obbligatorio solo se lo scope è privato, (separati da spazio)
            'code' => $this->string(), // Da valorizzare come "CAP-IT-VF:0.1", se valorizzato con quel codice è obbligatorio mettere gli incidents
            'note' => $this->text(),
            'incidents' => $this->string(), // identifier dell'evento (Se Actual è lui stesso, Se )
            'references' => $this->string(), // messaggio precedente (obbligatorio se non è Actual)
            // BLOCCO INFO
            'language' => $this->string()->defaultValue('it-IT'),
            'category' => $this->string()->defaultValue('Other'), //Geo;Met;Safety;Security;Rescue;Fire;Health;Env;Transport;Infra;CBRNE;Other
            'event' => $this->string(),// tipo evento obbligatorio (mettiamo il tipo evento)
            'response_type' => $this->string()->defaultValue('None'), //“Shelter”, “Evacuate”, “Prepare”, “Execute”, “Avoid”, “Monitor”, “Assess”, “AllClear”, “None”
            'urgency' => $this->string()->defaultValue('Unknown'), // “Immediate”; “Expected”, “Future”, “Past”, “Unknown”
            'severity' => $this->string()->defaultValue('Unknown'), // “Extreme”, ”Severe”, ”Moderate”, ”Minor”, ”Unknown”
            'certainty' => $this->string()->defaultValue('Unknown'), // “Observed”, “Likely”, “Possible”, “Unlikely”, “Unknown”
            'audience' => $this->string(), // lasciamo vuoto
            'eventCode' => $this->json(), // Code_L1 => tipo evento, Code_L2 => sottotipo evento
            'effective' => 'TIMESTAMP WITH TIME ZONE', // dataora messaggio
            'onset' => 'TIMESTAMP WITH TIME ZONE', // dataora creazione evento
            'expires' => 'TIMESTAMP WITH TIME ZONE', // lasciamo vuoto, non abbiamo questo parametro
            'senderName' => $this->string(), // precompilato da params
            'headline' => $this->string(), // descrizione breve
            'description' => $this->text(),
            'instruction' => $this->text(),
            'web' => $this->string(), // mappa coordinate
            'contact' => $this->string(), // precompilato da params
            'parameter' => $this->json(), 
                /*
                CODEINT {id evento}
                TIMECALL {data creazione}
                TIMEINT {data passaggio in gestione}
                TIMECANC  {dataora chiusura}
                INCIDENTPROGRESS {stato evento stringa}
                MAJOREVENT {N se fronte, Y se evento}
                REFNUM {protocollo evento}
                VEHICLES {lista veicoli}
                 */
            'resource' => $this->text(),
                /*
                resourceDesc {Nome file}
                mimeType 
                derefUri {base64 file}
                digest {hash sha1 del file}
                */
            'area' => $this->json(),
                /*
                areaDesc {indirizzo/luogo}
                circle {lat,lon,0.01}
                geocode
                    valueName = ZIP
                    value {cap}
                 */
            'lat' => $this->float(),
            'lon' => $this->float(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->addColumn('utl_tipologia', 'cap_category', $this->string()->defaultValue('Other'));
        $this->addColumn('utl_evento', 'dataora_gestione', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_evento', 'dataora_gestione');
        $this->dropColumn('utl_tipologia', 'cap_category');
        $this->dropTable('cap_exposed_message');
    }
}
