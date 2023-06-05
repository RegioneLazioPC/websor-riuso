<?php

use yii\db\Migration;

/**
 * Class m220912_094615_alter_view_cap_vehicles
 */
class m220912_094615_alter_view_cap_vehicles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_cap_vehicles AS
            SELECT view_cap_messages_grouped.id,
                view_cap_messages_grouped.identifier,
                view_cap_messages_grouped.incident,
                view_cap_messages_grouped.expires_rome_timezone,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[1] AS targa,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[2] AS tipo_mezzo,
                nullif( nullif( (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[3], 'null' ), '') AS data_attivazione,
                nullif( nullif( (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[4], 'null' ), '') AS data_arrivo,
                nullif( nullif( (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[5], 'null' ), '') AS data_chiusura,
                nullif( nullif( (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[6], 'null' ), '') AS data_deviazione,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[7] AS identificativo_attivazione,
                view_cap_messages_grouped.status
               FROM view_cap_messages_grouped
                 CROSS JOIN LATERAL json_array_elements((((view_cap_messages_grouped.json_content ->> 'info'::text)::jsonb) ->> 'parameter'::text)::json) e(value)
              WHERE view_cap_messages_grouped.expires_rome_timezone > now() AND (e.value ->> 'valueName'::text) = 'VEHICLES'::text;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_cap_vehicles AS
            SELECT view_cap_messages_grouped.id,
                view_cap_messages_grouped.identifier,
                view_cap_messages_grouped.incident,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[1] AS targa,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[2] AS tipo_mezzo,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[3] AS data_attivazione,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[4] AS data_arrivo,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[5] AS data_chiusura,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[6] AS data_deviazione,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[7] AS identificativo_attivazione,
                view_cap_messages_grouped.status
               FROM view_cap_messages_grouped
                 CROSS JOIN LATERAL json_array_elements((((view_cap_messages_grouped.json_content ->> 'info'::text)::jsonb) ->> 'parameter'::text)::json) e(value)
              WHERE view_cap_messages_grouped.expires_rome_timezone > now() AND (e.value ->> 'valueName'::text) = 'VEHICLES'::text;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220912_094615_alter_view_cap_vehicles cannot be reverted.\n";

        return false;
    }
    */
}
