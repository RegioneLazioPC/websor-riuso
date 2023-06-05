<?php

use yii\db\Migration;

/**
 * Class m200925_143035_alter_dialect_to_profile
 */
class m200925_143035_alter_dialect_to_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TABLE cap_resources 
        RENAME dialect TO profile;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE cap_resources 
        RENAME profile TO dialect;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_143035_alter_dialect_to_profile cannot be reverted.\n";

        return false;
    }
    */
}
