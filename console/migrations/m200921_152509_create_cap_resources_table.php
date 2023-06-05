<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `cap_resources`.
 */
class m200921_152509_create_cap_resources_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cap_resources', [
            'id' => $this->primaryKey(),
            'identifier' => $this->string(),
            'url_feed_rss' => $this->string(1000),
            'url_feed_atom' => $this->string(1000),
            'preferred_feed' => $this->string(4),
            'dialect' => $this->string(10), // standard, vvff
            'raggruppamento' => $this->string(),
            'autenticazione' => $this->string(),
            'username' => $this->string(),
            'password' => $this->string(1000),
            'last_check' => $this->integer(20),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        Yii::$app->db->createCommand("CREATE VIEW view_cap_raggruppamenti AS 
            SELECT distinct raggruppamento FROM cap_resources
        ")->execute();

        $this->addColumn('cap_messages', 'id_resource', $this->integer());
        $this->addForeignKey(
            'fk-cap_message_resource',
            'cap_messages',
            'id_resource',
            'cap_resources', 
            'id',
            'SET NULL'
        );

        $this->addColumn('cap_messages', 'status', $this->string());
        $this->addColumn('cap_messages', 'scheda', $this->string());
        $this->addColumn('cap_messages', 'scheda_update', $this->string());
        $this->addColumn('cap_messages', 'incident', $this->string());
        $this->addColumn('cap_messages', 'sender', $this->string());
        $this->addColumn('cap_messages', 'sender_name', $this->string());
        $this->addColumn('cap_messages', 'category', $this->string());
        $this->addColumn('cap_messages', 'description', $this->string());
        $this->addColumn('cap_messages', 'event', $this->string());
        $this->addColumn('cap_messages', 'event_type', $this->string());
        $this->addColumn('cap_messages', 'event_subtype', $this->string());
        $this->addColumn('cap_messages', 'segnalatore', $this->string());
        $this->addColumn('cap_messages', 'sent', 'TIMESTAMP WITH TIME ZONE');
        $this->addColumn('cap_messages', 'sent_rome_timezone', $this->datetime());


        /*
        

        Yii::$app->db->createCommand("CREATE VIEW view_cap_messages AS 
            select
            id,
            json_content->>'status' as status,
            \"type\" as msgType,
            COALESCE(substring(identifier from '.*\.([0-9]*)\.[0-9]+$'), identifier) as scheda,
            COALESCE(substring(identifier from '.*\.[0-9]*\.([0-9]+)$'), identifier) as scheda_update,
            COALESCE(substring(json_content->>'incidents' from '.*\,(.*)\,.*$'), json_content->>'incidents') as incident,
            json_content->>'sender' as sender,
            json_content->'info'->>'senderName' as senderName,
            json_content->'info'->>'category' as category,
            json_content->'info'->>'description' as description,
            json_content->'info'->>'event' as event,
            CASE 
                WHEN jsonb_typeof(json_content->'info'->'eventCode') ='array' THEN (
                    select
                        json_object_agg( COALESCE(el->>'valueName','test'), el->'value')->>'Code_L1'
                    from
                        jsonb_array_elements(json_content->'info'->'eventCode') el
                    )
                ELSE (select
                        json_object_agg( 
                            el->>'valueName', 
                            el->>'value'
                        )->>'Code_L1'
                        FROM jsonb(json_content->'info'->'eventCode') el
                    )
            END as tipo_evento,
            CASE 
                WHEN jsonb_typeof(json_content->'info'->'eventCode') ='array' THEN (
                    select
                        json_object_agg( COALESCE(el->>'valueName','test'), el->'value')->>'Code_L2'
                    from
                        jsonb_array_elements(json_content->'info'->'eventCode') el
                    )
                ELSE (select
                        json_object_agg( 
                            el->>'valueName', 
                            el->>'value'
                        )->>'Code_L2'
                        FROM jsonb(json_content->'info'->'eventCode') el
                    )
            END as sottotipo_evento,
            json_content->>'source' as segnalatore,
            cap_feed_url as url_cap,
            (json_content->>'sent')::TIMESTAMP as sent,
            to_timestamp(json_content->>'sent', 'YYYY-MM-DD hh24:mi:ss')::timestamp without time zone at time zone 'Europe/Rome' as sent_with_rome_time,
            json_content,
            xml_content
            from cap_messages;")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cap_messages_grouped AS 
            WITH all_cap_messages AS (
                SELECT vm.*, 
                       ROW_NUMBER() OVER(PARTITION BY vm.incident 
                                             ORDER BY vm.sent_with_rome_time DESC) AS rk
                  FROM view_cap_messages vm)
            SELECT s.*
              FROM all_cap_messages s
             WHERE s.rk = 1
             ORDER BY sent_with_rome_time DESC;")->execute();
             */
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /*Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();*/
        
        $this->dropForeignKey(
            'fk-cap_message_resource',
            'cap_messages'
        );

        $this->dropColumn('cap_messages', 'id_resource');
        $this->dropColumn('cap_messages', 'status');
        $this->dropColumn('cap_messages', 'scheda');
        $this->dropColumn('cap_messages', 'scheda_update');
        $this->dropColumn('cap_messages', 'incident');
        $this->dropColumn('cap_messages', 'sender');
        $this->dropColumn('cap_messages', 'sender_name');
        $this->dropColumn('cap_messages', 'category');
        $this->dropColumn('cap_messages', 'description');
        $this->dropColumn('cap_messages', 'event');
        $this->dropColumn('cap_messages', 'event_type');
        $this->dropColumn('cap_messages', 'event_subtype');
        $this->dropColumn('cap_messages', 'segnalatore');
        $this->dropColumn('cap_messages', 'sent');
        $this->dropColumn('cap_messages', 'sent_rome_timezone');

        Yii::$app->db->createCommand("DROP VIEW view_cap_raggruppamenti")->execute();
        $this->dropTable('cap_resources');

    }
}
