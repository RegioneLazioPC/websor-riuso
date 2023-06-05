<?php

use yii\db\Migration;

/**
 * Class m210429_232944_add_action_fire_cap
 */
class m210429_232944_add_action_fire_cap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cap_exposed_message', 'action_fire', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cap_exposed_message', 'action_fire');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210429_232944_add_action_fire_cap cannot be reverted.\n";

        return false;
    }
    */
}
