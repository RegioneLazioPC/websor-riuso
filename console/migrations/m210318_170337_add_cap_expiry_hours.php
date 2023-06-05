<?php

use yii\db\Migration;

/**
 * Class m210318_170337_add_cap_expiry_hours
 */
class m210318_170337_add_cap_expiry_hours extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();
        
        $this->addColumn('cap_resources', 'expiry', $this->integer());
        $this->addColumn('cap_messages', 'expires', 'TIMESTAMP WITH TIME ZONE');

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
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();
        
        $this->dropColumn('cap_resources', 'expiry');
        $this->dropColumn('cap_messages', 'expires');

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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210318_170337_add_cap_expiry_hours cannot be reverted.\n";

        return false;
    }
    */
}
