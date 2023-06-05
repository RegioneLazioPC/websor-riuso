<?php

use yii\db\Migration;

/**
 * Class m210902_125348_add_manual_flag_task_mattinale
 */
class m210902_125348_add_manual_flag_task_mattinale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_operatore_task', 'manual_flag', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_operatore_task', 'manual_flag');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210902_125348_add_manual_flag_task_mattinale cannot be reverted.\n";

        return false;
    }
    */
}
