<?php

use yii\db\Migration;

/**
 * Class m201002_110007_add_columns_for_cap
 */
class m201002_110007_add_columns_for_cap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();
        
        $this->addColumn('cap_messages', 'call_time', 'TIMESTAMP WITH TIME ZONE');
        $this->addColumn('cap_messages', 'intervent_time', 'TIMESTAMP WITH TIME ZONE');
        $this->addColumn('cap_messages', 'arrival_time', 'TIMESTAMP WITH TIME ZONE');
        $this->addColumn('cap_messages', 'close_time', 'TIMESTAMP WITH TIME ZONE');
        $this->addColumn('cap_messages', 'major_event', $this->integer());
        $this->addColumn('cap_messages', 'profile', $this->string());
        $this->addColumn('cap_messages', 'code_int', $this->string());
        $this->addColumn('cap_messages', 'code_call', $this->string());
        $this->addColumn('cap_messages', 'string_comune', $this->string());
        $this->addColumn('cap_messages', 'id_comune', $this->integer());
        $this->addColumn('cap_messages', 'string_provincia', $this->string());
        $this->addColumn('cap_messages', 'id_provincia', $this->integer());
        $this->addColumn('cap_messages', 'formatted_status', $this->string());

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
        
        $this->dropColumn('cap_messages', 'call_time');
        $this->dropColumn('cap_messages', 'intervent_time');
        $this->dropColumn('cap_messages', 'arrival_time');
        $this->dropColumn('cap_messages', 'close_time');
        $this->dropColumn('cap_messages', 'major_event');
        $this->dropColumn('cap_messages', 'profile');
        $this->dropColumn('cap_messages', 'code_int');
        $this->dropColumn('cap_messages', 'code_call');
        $this->dropColumn('cap_messages', 'string_comune');
        $this->dropColumn('cap_messages', 'id_comune');
        $this->dropColumn('cap_messages', 'string_provincia');
        $this->dropColumn('cap_messages', 'id_provincia');
        $this->dropColumn('cap_messages', 'formatted_status');

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
        echo "m201002_110007_add_columns_for_cap cannot be reverted.\n";

        return false;
    }
    */
}
