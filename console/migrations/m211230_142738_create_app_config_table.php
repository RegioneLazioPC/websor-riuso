<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%app_config}}`.
 */
class m211230_142738_create_app_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('app_config', [
            'id' => $this->primaryKey(),
            'label' => $this->string(),
            'key' => $this->string()->unique(),
            'value' => $this->string(10000),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('app_config');
    }
}
