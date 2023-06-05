<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230330_171456_create_access_logs_tables
 */
class m230330_171456_create_access_logs_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('app_access_log', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(),
            'username' => $this->string(1000),
            'ip' => $this->string(),
            'email' => $this->string(),
            'action' => $this->string(),
            'meta' => $this->json(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('app_access_log');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230330_171456_create_access_logs_tables cannot be reverted.\n";

        return false;
    }
    */
}
