<?php

use yii\db\Migration;

/**
 * Class m220512_171717_alter_view_cap_messages
 */
class m220512_171717_alter_view_cap_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("CREATE OR REPLACE  VIEW view_cap_messages_grouped AS 
            SELECT s.risorsa,
    s.raggruppamento,
    s.incident,
    s.id,
    s.cap_feed_url,
    s.url,
    s.identifier,
    s.type,
    s.xml_content,
    s.json_content,
    s.date_creation,
    s.id_resource,
    s.status,
    s.scheda,
    s.scheda_update,
    s.sender,
    s.sender_name,
    s.category,
    s.description,
    s.event,
    s.event_type,
    s.event_subtype,
    s.segnalatore,
    s.sent,
    s.sent_rome_timezone,
    s.poly_geom,
    s.lat,
    s.lon,
    s.center_geom,
    s.info_n,
    s.call_time,
    s.intervent_time,
    s.arrival_time,
    s.close_time,
    s.major_event,
    s.profile,
    s.code_int,
    s.code_call,
    s.string_comune,
    s.id_comune,
    s.string_provincia,
    s.id_provincia,
    s.formatted_status,
    s.expires,
    s.rk,
        CASE
            WHEN s.expires IS NULL AND r.expiry IS NOT NULL THEN s.sent_rome_timezone + '01:00:00'::interval * r.expiry::double precision
            ELSE timezone('CET'::text, s.expires)
        END AS expires_rome_timezone
   FROM ( SELECT vm.risorsa,
            vm.raggruppamento,
            vm.incident,
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
            row_number() OVER (PARTITION BY vm.incident ORDER BY vm.sent_rome_timezone DESC) AS rk
           FROM view_cap_messages vm) s
     LEFT JOIN cap_resources r ON r.url_feed_rss::text = s.cap_feed_url::text OR r.url_feed_atom::text = s.cap_feed_url::text
  WHERE s.rk = 1
  ORDER BY s.sent_rome_timezone DESC;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_171717_alter_view_cap_messages cannot be reverted.\n";

        return false;
    }
    */
}
