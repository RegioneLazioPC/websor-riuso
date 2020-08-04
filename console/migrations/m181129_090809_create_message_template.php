<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181129_090809_create_message_template
 */
class m181129_090809_create_message_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Template messaggio
         */
        $this->createTable('mas_message_template', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'mail_body' => $this->text(),
            'sms_body' => $this->string(140),
            'push_body' => $this->string(),
            'fax_body' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        /**
         * Messaggio
         *
         * per ogni messaggio dovranno scegliere quale canale usare
         */
        $this->createTable('mas_message', [
            'id' => $this->primaryKey(),
            'id_template' => $this->integer(),
            'id_allerta' => $this->integer(),
            'note' => $this->string(),
            'channel_mail' => $this->integer(1),
            'channel_pec' => $this->integer(1),
            'channel_push' => $this->integer(1),
            'channel_sms' => $this->integer(1),
            'channel_fax' => $this->integer(1),
            'mail_text' => $this->text(),
            'sms_text' => $this->string(140),
            'push_text' => $this->string(),
            'fax_text' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->addForeignKey(
            'fk-mas_message_template',
            'mas_message',
            'id_template',
            'mas_message_template',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-mas_message_allerta',
            'mas_message',
            'id_allerta',
            'alm_allerta_meteo',
            'id',
            'SET NULL'
        );

        $this->createTable('con_mas_message_rubrica_group', [
            'id' => $this->primaryKey(),
            'id_message' => $this->integer(),
            'id_group' => $this->integer()
        ]);

        /**
         * Stesso discorso di con_rubrica_group_contact
         */
        $this->createTable('con_mas_message_contact', [
            'id' => $this->primaryKey(),
            'id_message' => $this->integer(),
            'id_rubrica_contatto' => $this->integer(), // rapporto con elemento della vista 
            'tipo_rubrica_contatto' => $this->integer() // rapporto con elemento della vista 
        ]);

        /**
         * Salvo un tentativo di invio che raggruppi tutti i contatti per i quali è stato effettuato
         *
         * alla creazione dell'invio andrò a prendere dal message gruppi e contatti associati
         * e li associo anche all'invio
         *
         * poi servirà una grid per ogni invio con la lista dei single_send, per tenere traccia
         * degli invii a buon fine o meno
         * 
         */
        $this->createTable('mas_invio', [
            'id' => $this->primaryKey(),
            'id_message' => $this->integer(),
            'data_invio' => $this->date(),
            'channel_mail' => $this->integer(1),
            'channel_pec' => $this->integer(1),
            'channel_push' => $this->integer(1),
            'channel_sms' => $this->integer(1),
            'channel_fax' => $this->integer(1)
        ]);

        /**
         * Connessione tra invio e gruppo
         * 
         */
        $this->createTable('con_mas_invio_rubrica_group', [
            'id' => $this->primaryKey(),
            'id_invio' => $this->integer(),
            'id_group' => $this->integer()
        ]);

        /**
         * Connessione invio e singolo contatto
         * Stesso discorso di con_rubrica_group_contact
         */
        $this->createTable('con_mas_invio_contact', [
            'id' => $this->primaryKey(),
            'id_invio' => $this->integer(),
            'id_rubrica_contatto' => $this->integer(), // rapporto con elemento della vista 
            'tipo_rubrica_contatto' => $this->integer() // rapporto con elemento della vista 
        ]);

        $this->createTable('mas_single_send', [
            'id' => $this->primaryKey(),
            'id_invio' => $this->integer(),
            //'id_contatto' => $this->integer(), // qui andremo a inserire invece il rif al singolo contatto, potrebbe creare problemi con le push in cui dovrebbe connettere il device da utl_utente
            'id_rubrica_contatto' => $this->integer(), // rapporto con elemento della vista 
            'tipo_rubrica_contatto' => $this->integer(), // rapporto con elemento della vista 
            'channel' => $this->integer(), // questo ci permette di filtrare in base al canale
            'status' => $this->integer(), // 0-1 sent, refused, undeliverable
            'sending_attempts' => $this->integer(), // numero di tentativi di invio
            'last_attempt' => $this->datetime() // data ultimo tentativo
        ]);

        /**
         * Creazione gruppi rubrica
         */
        $this->createTable('rubrica_group',[
            'id' => $this->primaryKey(),
            'name' => $this->string()
        ]);

        /**
         * Connessione gruppo -> rubrica
         *
         * visto che i dati dei contatti vengono da una vista e non ho id univoci
         * uso la doppia chiave id_rubrica_contatto - tipo_rubrica_contatto 
         * per identificare il riferimento
         *
         * dovremo ricordare in creazione della vista di inserire un identifier per 
         * id_rubrica_contatto mentre il tipo sarà definito in base all'elemento della vista
         */
        $this->createTable('con_rubrica_group_contact', [
            'id' => $this->primaryKey(),
            'id_group' => $this->integer(),
            'id_rubrica_contatto' => $this->integer(), // rapporto con elemento della vista 
            'tipo_rubrica_contatto' => $this->integer() // rapporto con elemento della vista 
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey(
            'fk-mas_message_template',
            'mas_message'
        );

        $this->dropForeignKey(
            'fk-mas_message_allerta',
            'mas_message'
        );

        $this->dropTable('con_mas_message_rubrica_group');
        $this->dropTable('con_mas_message_contact');
        $this->dropTable('mas_invio');
        $this->dropTable('con_mas_invio_rubrica_group');
        $this->dropTable('con_mas_invio_contact');
        $this->dropTable('mas_single_send');
        $this->dropTable('rubrica_group');
        $this->dropTable('con_rubrica_group_contact');
        $this->dropTable('mas_message');
        $this->dropTable('mas_message_template');
    }

}
