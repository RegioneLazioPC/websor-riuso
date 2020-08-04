<?php

use yii\db\Migration;

/**
 * Class m190516_160746_add_unique_index_feedback_mas_single_send
 */
class m190516_160746_add_unique_index_feedback_mas_single_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_id_feedback_mas_single_send ON mas_single_send (
            id_feedback)")
        ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("
            DROP INDEX IF EXISTS unique_id_feedback_mas_single_send")
        ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_160746_add_unique_index_feedback_mas_single_send cannot be reverted.\n";

        return false;
    }
    */
}
