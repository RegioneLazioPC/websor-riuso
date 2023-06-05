<?php

use yii\db\Migration;

/**
 * Class m220912_092128_create_view_cap_vehicles_history
 */
class m220912_092128_create_view_cap_vehicles_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_cap_vehicles_history AS
            SELECT view_cap_messages_grouped.id,
                view_cap_messages_grouped.identifier,
                view_cap_messages_grouped.incident,
                view_cap_messages_grouped.expires_rome_timezone,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[1] AS targa,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[2] AS tipo_mezzo,
                NULLIF(NULLIF((regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[3], 'null'::text), ''::text) AS data_attivazione,
                NULLIF(NULLIF((regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[4], 'null'::text), ''::text) AS data_arrivo,
                NULLIF(NULLIF((regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[5], 'null'::text), ''::text) AS data_chiusura,
                NULLIF(NULLIF((regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[6], 'null'::text), ''::text) AS data_deviazione,
                (regexp_matches(e.value ->> 'value'::text, '[ ]{0,1}([^,]+),([^,\"]*|[\" ]+[^\"]*[\" ]+),([^, ]*),([^, ]*),([^, ]*),([^, ]*),([^, ]*)'::text, 'g'::text))[7] AS identificativo_attivazione,
                view_cap_messages_grouped.status
               FROM view_cap_messages_grouped
                 CROSS JOIN LATERAL json_array_elements((((view_cap_messages_grouped.json_content ->> 'info'::text)::jsonb) ->> 'parameter'::text)::json) e(value)
                WHERE (e.value ->> 'valueName'::text) = 'VEHICLES'::text
             ;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles_history")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220912_092128_create_view_cap_vehicles_history cannot be reverted.\n";

        return false;
    }
    */
}
