<?php

use yii\db\Migration;

/**
 * Class m220513_094158_optimize_cap
 */
class m220513_094158_optimize_cap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->addColumn('con_cap_message_incident', 'sent_rome_timezone', $this->datetime());

        Yii::$app->db->createCommand("UPDATE con_cap_message_incident SET sent_rome_timezone = (SELECT sent_rome_timezone FROM cap_messages WHERE id = id_cap_message)")->execute();

        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW view_cap_messages_grouped AS
            SELECT distinct on (incident) 
            r.identifier AS risorsa,
            r.raggruppamento,
            incident,
            vm.id,
            vm.cap_feed_url,
            vm.url,
            vm.identifier,
            vm.type,
            vm.xml_content,
            vm.json_content,
            vm.date_creation,
            vm.id_resource,
            vm.status,
            vm.scheda,
            vm.scheda_update,
            vm.sender,
            vm.sender_name,
            vm.category,
            vm.description,
            vm.event,
            vm.event_type,
            vm.event_subtype,
            vm.segnalatore,
            vm.sent,
            vm.sent_rome_timezone,
            vm.poly_geom,
            vm.lat,
            vm.lon,
            vm.center_geom,
            vm.info_n,
            vm.call_time,
            vm.intervent_time,
            vm.arrival_time,
            vm.close_time,
            vm.major_event,
            vm.profile,
            vm.code_int,
            vm.code_call,
            vm.string_comune,
            vm.id_comune,
            vm.string_provincia,
            vm.id_provincia,
            vm.formatted_status,
            vm.expires,
            1::bigint AS rk,
                CASE
                    WHEN vm.expires IS NULL AND r.expiry IS NOT NULL THEN vm.sent_rome_timezone + '01:00:00'::interval * r.expiry::double precision
                    ELSE timezone('CET'::text, vm.expires)
                END AS expires_rome_timezone
                FROM con_cap_message_incident ci
                LEFT JOIN cap_messages vm ON vm.id = ci.id_cap_message
                LEFT JOIN cap_resources r ON r.id = vm.id_resource
                ORDER  BY incident, sent_rome_timezone DESC, id_cap_message")->execute();

        Yii::$app->db->createCommand("CREATE INDEX idx_cap_m_i_sent_rome_timezone ON con_cap_message_incident(incident, sent_rome_timezone DESC, id_cap_message);")->execute();
        Yii::$app->db->createCommand("CREATE INDEX idx_cap_m_sent_rome_timezone ON cap_messages(sent_rome_timezone);")->execute();
        Yii::$app->db->createCommand("CREATE INDEX idx_cap_raggruppamento ON cap_resources(raggruppamento);")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP INDEX idx_cap_raggruppamento;")->execute();
        Yii::$app->db->createCommand("DROP INDEX idx_cap_m_sent_rome_timezone;")->execute();
        Yii::$app->db->createCommand("DROP INDEX idx_cap_m_i_sent_rome_timezone;")->execute();
        
        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW view_cap_messages_grouped AS 
            SELECT r.identifier AS risorsa,
            r.raggruppamento,
            ci.incident,
            vm.id,
            vm.cap_feed_url,
            vm.url,
            vm.identifier,
            vm.type,
            vm.xml_content,
            vm.json_content,
            vm.date_creation,
            vm.id_resource,
            vm.status,
            vm.scheda,
            vm.scheda_update,
            vm.sender,
            vm.sender_name,
            vm.category,
            vm.description,
            vm.event,
            vm.event_type,
            vm.event_subtype,
            vm.segnalatore,
            vm.sent,
            vm.sent_rome_timezone,
            vm.poly_geom,
            vm.lat,
            vm.lon,
            vm.center_geom,
            vm.info_n,
            vm.call_time,
            vm.intervent_time,
            vm.arrival_time,
            vm.close_time,
            vm.major_event,
            vm.profile,
            vm.code_int,
            vm.code_call,
            vm.string_comune,
            vm.id_comune,
            vm.string_provincia,
            vm.id_provincia,
            vm.formatted_status,
            vm.expires,
            1::BIGINT rk,
               CASE
                    WHEN vm.expires IS NULL AND r.expiry IS NOT NULL THEN vm.sent_rome_timezone + '01:00:00'::interval * r.expiry::double precision
                    ELSE timezone('CET'::text, vm.expires)
                END AS expires_rome_timezone
        FROM (SELECT incident, ARRAY_AGG(id_cap_message) ids FROM con_cap_message_incident GROUP BY incident) ci 
        CROSS JOIN LATERAL (SELECT * FROM cap_messages WHERE id = ANY( ci.ids ) ORDER BY sent_rome_timezone DESC LIMIT 1) vm 
        LEFT JOIN cap_resources r ON r.id = vm.id_resource
        ORDER BY vm.sent_rome_timezone DESC
        ;")->execute();

        $this->dropColumn('con_cap_message_incident', 'sent_rome_timezone');
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220513_094158_optimize_cap cannot be reverted.\n";

        return false;
    }
    */
}
