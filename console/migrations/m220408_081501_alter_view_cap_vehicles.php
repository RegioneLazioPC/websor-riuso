<?php

use yii\db\Migration;

/**
 * Class m220408_081501_alter_view_cap_vehicles
 */
class m220408_081501_alter_view_cap_vehicles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles")->execute();
        
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
        Yii::$app->db->createCommand('CREATE VIEW view_cap_vehicles AS 
            SELECT
                view_cap_messages_grouped.id,
                view_cap_messages_grouped.identifier,
                view_cap_messages_grouped.incident,
                split_part( unnest( regexp_split_to_array( e->>\'value\', E\'\\\\s(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)\') ), \',\'::text, 1) AS targa
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
                view_cap_messages_grouped.expires_rome_timezone > now() AND
                (e.value ->> \'valueName\'::text) ~~ \'%VEHICLES%\'::text;')->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_081501_alter_view_cap_vehicles cannot be reverted.\n";

        return false;
    }
    */
}
