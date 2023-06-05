<?php

use yii\db\Migration;

/**
 * Class m220407_154625_create_view_mezzi_attivati
 */
class m220407_154625_create_view_mezzi_attivati extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_cap_vehicles AS 
            SELECT id, identifier, split_part( e.value->>'value', ',', 1) as targa 
            FROM view_cap_messages_grouped
            CROSS JOIN LATERAL json_array_elements( ( (json_content->>'info')::jsonb->>'parameter')::json) as e
            where e ->> 'valueName' like '%VEHICLES%' AND expires_rome_timezone > now();")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_154625_create_view_mezzi_attivati cannot be reverted.\n";

        return false;
    }
    */
}
