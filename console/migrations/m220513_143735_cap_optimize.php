<?php

use yii\db\Migration;

/**
 * Class m220513_143735_cap_optimize
 */
class m220513_143735_cap_optimize extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("create index \"idx_capmessage_incident_sent\" on cap_messages(((regexp_split_to_array(json_content ->> 'incidents'::text, ','::text))[2]), sent_rome_timezone DESC);")->execute();

        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles")->execute();
        
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_cap_messages_grouped_a")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();
        
        Yii::$app->db->createCommand("create or replace view view_cap_messages as
         SELECT r.identifier AS risorsa,
            r.raggruppamento,
            (regexp_split_to_array(m.json_content ->> 'incidents'::text, ','::text))[2] AS incident,
            m.id,
            m.cap_feed_url,
            m.url,
            m.identifier,
            m.type,
            m.xml_content,
            m.json_content,
            m.date_creation,
            m.id_resource,
            m.status,
            m.scheda,
            m.scheda_update,
            m.sender,
            m.sender_name,
            m.category,
            m.description,
            m.event,
            m.event_type,
            m.event_subtype,
            m.segnalatore,
            m.sent,
            m.sent_rome_timezone,
            m.poly_geom,
            m.lat,
            m.lon,
            m.center_geom,
            m.info_n,
            m.call_time,
            m.intervent_time,
            m.arrival_time,
            m.close_time,
            m.major_event,
            m.profile,
            m.code_int,
            m.code_call,
            m.string_comune,
            m.id_comune,
            m.string_provincia,
            m.id_provincia,
            m.formatted_status,
            m.expires
           FROM cap_messages m
             LEFT JOIN cap_resources r ON r.id = m.id_resource;")->execute();

        Yii::$app->db->createCommand("Create or replace view view_cap_messages_grouped as 
            SELECT vm.identifier AS risorsa,
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
            1::BIGINT rk,
               CASE
                    WHEN vm.expires IS NULL AND r.expiry IS NOT NULL THEN vm.sent_rome_timezone + '01:00:00'::interval * r.expiry::double precision
                    ELSE timezone('CET'::text, vm.expires)
                END AS expires_rome_timezone
          FROM
          view_cap_messages vm
          LEFT JOIN cap_resources r ON r.url_feed_rss::text = vm.cap_feed_url::text OR r.url_feed_atom::text = vm.cap_feed_url::text
          WHERE
          NOT EXISTS (select 1 from view_cap_messages a where a.incident= vm.incident and ((a.sent_rome_timezone > vm.sent_rome_timezone) OR (a.sent_rome_timezone = vm.sent_rome_timezone and a.id > vm.id)))
        ;")->execute();

        Yii::$app->db->createCommand('CREATE VIEW view_cap_vehicles AS 
            SELECT
            view_cap_messages_grouped.id,
            view_cap_messages_grouped.identifier,
            view_cap_messages_grouped.incident,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[1] as targa,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[2] as tipo_mezzo,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[3] as data_attivazione,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[4] as data_arrivo,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[5] as data_chiusura,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[6] as data_deviazione,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[7] as identificativo_attivazione,
            view_cap_messages_grouped.status
            FROM
            view_cap_messages_grouped
            CROSS JOIN LATERAL json_array_elements(
                (
                    (
                        (view_cap_messages_grouped.json_content ->> \'info\'::text)::jsonb
                    ) ->> \'parameter\'::text
                )::json
            ) e
        WHERE
            view_cap_messages_grouped.expires_rome_timezone > now()')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_cap_messages_grouped_a")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();

        Yii::$app->db->createCommand("DROP INDEX idx_capmessage_incident_sent")->execute();
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

        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW view_cap_messages AS 
            SELECT r.identifier AS risorsa,
            r.raggruppamento,
            ci.incident,
            m.id,
            m.cap_feed_url,
            m.url,
            m.identifier,
            m.type,
            m.xml_content,
            m.json_content,
            m.date_creation,
            m.id_resource,
            m.status,
            m.scheda,
            m.scheda_update,
            m.sender,
            m.sender_name,
            m.category,
            m.description,
            m.event,
            m.event_type,
            m.event_subtype,
            m.segnalatore,
            m.sent,
            m.sent_rome_timezone,
            m.poly_geom,
            m.lat,
            m.lon,
            m.center_geom,
            m.info_n,
            m.call_time,
            m.intervent_time,
            m.arrival_time,
            m.close_time,
            m.major_event,
            m.profile,
            m.code_int,
            m.code_call,
            m.string_comune,
            m.id_comune,
            m.string_provincia,
            m.id_provincia,
            m.formatted_status,
            m.expires
           FROM cap_messages m
             LEFT JOIN cap_resources r ON r.id = m.id_resource
             LEFT JOIN con_cap_message_incident ci ON ci.id_cap_message = m.id;")->execute();

        Yii::$app->db->createCommand('CREATE VIEW view_cap_vehicles AS 
            SELECT
            view_cap_messages_grouped.id,
            view_cap_messages_grouped.identifier,
            view_cap_messages_grouped.incident,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[1] as targa,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[2] as tipo_mezzo,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[3] as data_attivazione,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[4] as data_arrivo,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[5] as data_chiusura,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[6] as data_deviazione,
            (regexp_matches(e->>\'value\',E\'[ ]{0,1}([^,]+),([^,"]*|[" ]+[^"]*[" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)\',\'g\'))[7] as identificativo_attivazione,
            view_cap_messages_grouped.status
            FROM
            view_cap_messages_grouped
            CROSS JOIN LATERAL json_array_elements(
                (
                    (
                        (view_cap_messages_grouped.json_content ->> \'info\'::text)::jsonb
                    ) ->> \'parameter\'::text
                )::json
            ) e
        WHERE
            view_cap_messages_grouped.expires_rome_timezone > now()')->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220513_143735_cap_optimize cannot be reverted.\n";

        return false;
    }
    */
}
