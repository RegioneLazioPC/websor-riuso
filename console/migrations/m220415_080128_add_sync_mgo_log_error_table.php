<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m220415_080128_add_sync_mgo_log_error_table
 */
class m220415_080128_add_sync_mgo_log_error_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('app_sync_error_log', [
            'id' => $this->primaryKey(),
            'level' => $this->string(),
            'service' => $this->string(),
            'stack' => $this->text(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('app_sync_error_log');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220415_080128_add_sync_mgo_log_error_table cannot be reverted.\n";

        return false;
    }
    */
}
