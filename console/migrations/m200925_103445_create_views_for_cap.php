<?php

use yii\db\Migration;

/**
 * Class m200925_103445_create_views_for_cap
 */
class m200925_103445_create_views_for_cap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        Yii::$app->db->createCommand("
            CREATE VIEW view_cap_messages AS 
            SELECT r.identifier as risorsa, r.raggruppamento, ci.incident, m.* 
            FROM cap_messages m
                LEFT JOIN cap_resources r ON r.id = m.id_resource
                LEFT JOIN con_cap_message_incident ci ON ci.id_cap_message = m.id
                ;")
        ->execute();

        Yii::$app->db->createCommand("
            CREATE VIEW view_cap_messages_grouped AS 
            WITH all_cap_messages AS (
                SELECT vm.*, 
                    ROW_NUMBER() OVER(PARTITION BY vm.incident 
                    ORDER BY vm.sent_rome_timezone DESC) AS rk
                FROM view_cap_messages vm
            )
            SELECT s.*
                FROM all_cap_messages s
            WHERE s.rk = 1
            ORDER BY sent_rome_timezone DESC;")
        ->execute();


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped;")->execute();

        Yii::$app->db->createCommand("DROP VIEW view_cap_messages;")->execute();
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_103445_create_views_for_cap cannot be reverted.\n";

        return false;
    }
    */
}
