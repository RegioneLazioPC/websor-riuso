<?php

use yii\db\Migration;

/**
 * Class m220530_153832_remove_utl_task_foreign_key_cascade
 */
class m220530_153832_remove_utl_task_foreign_key_cascade extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'con_operatore_task_ibfk_3',
            'con_operatore_task'
        );
        $this->addForeignKey(
            'con_operatore_task_ibfk_3',
            'con_operatore_task',
            'idtask',
            'utl_task',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'con_operatore_task_ibfk_3',
            'con_operatore_task'
        );
        $this->addForeignKey(
            'con_operatore_task_ibfk_3',
            'con_operatore_task',
            'idtask',
            'utl_task',
            'id',
            'CASCADE'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220530_153832_remove_utl_task_foreign_key_cascade cannot be reverted.\n";

        return false;
    }
    */
}
