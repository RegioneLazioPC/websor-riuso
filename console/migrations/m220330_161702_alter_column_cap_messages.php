<?php

use yii\db\Migration;

/**
 * Class m220330_161702_alter_column_cap_messages
 */
class m220330_161702_alter_column_cap_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_cap_messages_grouped_a")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_cap_messages")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN description TYPE text;
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN status TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN scheda TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN scheda_update TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN type TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN event TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN event_type TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN event_subtype TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN segnalatore TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN sender TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN sender_name TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN category TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN profile TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN code_int TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN code_call TYPE varchar(1000);
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE cap_messages ALTER COLUMN formatted_status TYPE varchar(1000);
            ")->execute();


        Yii::$app->db->createCommand("CREATE VIEW view_cap_messages AS 
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
     LEFT JOIN con_cap_message_incident ci ON ci.id_cap_message = m.id;
            ")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cap_messages_grouped AS 
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


        Yii::$app->db->createCommand("CREATE VIEW view_cap_messages_grouped_a AS 
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
            1 AS rk
           FROM view_cap_messages vm,
            LATERAL ( SELECT view_cap_messages.incident,
                    view_cap_messages.sent_rome_timezone,
                    view_cap_messages.id
                   FROM view_cap_messages
                  WHERE view_cap_messages.incident::text = vm.incident::text
                  ORDER BY view_cap_messages.sent_rome_timezone DESC, view_cap_messages.id DESC
                 LIMIT 1) vm_mm
          WHERE vm.id = vm_mm.id) s
     LEFT JOIN cap_resources r ON r.url_feed_rss::text = s.cap_feed_url::text OR r.url_feed_atom::text = s.cap_feed_url::text
  ORDER BY s.sent_rome_timezone DESC;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_161702_alter_column_cap_messages cannot be reverted.\n";

        return false;
    }
    */
}
