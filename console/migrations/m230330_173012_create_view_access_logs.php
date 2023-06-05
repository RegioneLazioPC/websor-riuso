<?php

use yii\db\Migration;

/**
 * Class m230330_173012_create_view_access_logs
 */
class m230330_173012_create_view_access_logs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW vw_access_log AS 
            select lg.*, to_timestamp(lg.created_at) as datetime from auth_item rl
                left join auth_assignment aa on aa.item_name = rl.name
                left join app_access_log lg on lg.id_user = aa.user_id::integer
                where rl.administrative = 1 and lg.id is not null")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW vw_access_log")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230330_173012_create_view_access_logs cannot be reverted.\n";

        return false;
    }
    */
}
