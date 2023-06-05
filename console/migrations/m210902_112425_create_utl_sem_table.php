<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%utl_sem}}`.
 */
class m210902_112425_create_utl_sem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('utl_sem', [
            'id' => $this->primaryKey(),
            'object' => $this->string(),
            'lastuse' => 'TIMESTAMP WITHOUT TIME ZONE'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('utl_sem');
    }
}
