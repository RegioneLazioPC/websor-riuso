<?php

use yii\db\Migration;

/**
 * Class m210318_183931_alter_cap_views
 */
class m210318_183931_alter_cap_views extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped;")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages;")->execute();
        
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
            SELECT s.*,
                case when s.expires is null and r.expiry is not null
                    then s.sent_rome_timezone + interval '1h' * r.expiry
                else s.expires at time zone 'CET'
                end as expires_rome_timezone
                FROM (
                SELECT *,
                    ROW_NUMBER() over (PARTITION BY vm.incident 
                    ORDER BY vm.sent_rome_timezone DESC) as rk
                 FROM view_cap_messages vm
                ) s
                LEFT JOIN cap_resources r ON r.url_feed_rss = s.cap_feed_url OR r.url_feed_atom = s.cap_feed_url
             WHERE (s.rk = 1)
             ORDER BY sent_rome_timezone DESC;
             ")->execute();
        //Yii::$app->db->createCommand("
            //CREATE VIEW view_cap_messages_grouped AS 
            //WITH all_cap_messages AS (
                //SELECT vm.*, 
                    //ROW_NUMBER() OVER(PARTITION BY vm.incident 
                    //ORDER BY vm.sent_rome_timezone DESC) AS rk
                //FROM view_cap_messages vm
            //)
            //SELECT s.*,
                //case when s.expires is null and r.expiry is not null
                    //then s.sent_rome_timezone + interval '1h' * r.expiry
                //else s.expires at time zone 'CET'
                //end as expires_rome_timezone
                //FROM all_cap_messages s
                //LEFT JOIN cap_resources r ON r.url_feed_rss = s.cap_feed_url OR r.url_feed_atom = s.cap_feed_url
            //WHERE s.rk = 1
            //ORDER BY sent_rome_timezone DESC;")
        //->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages_grouped")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_messages")->execute();
        
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
        echo "m210318_183931_alter_cap_views cannot be reverted.\n";

        return false;
    }
    */
}
