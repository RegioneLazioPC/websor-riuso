<?php

use yii\db\Migration;

/**
 * Class m190510_135741_alter_sms_length
 */
class m190510_135741_alter_sms_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TABLE mas_message ALTER COLUMN sms_text TYPE varchar(255)"
        )->execute();

        Yii::$app->db->createCommand("ALTER TABLE mas_message_template ALTER COLUMN sms_body TYPE varchar(255)"
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE mas_message ALTER COLUMN sms_text TYPE varchar(140)"
        )->execute();

        Yii::$app->db->createCommand("ALTER TABLE mas_message_template ALTER COLUMN sms_body TYPE varchar(140)"
        )->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_135741_alter_sms_length cannot be reverted.\n";

        return false;
    }
    */
}
