<?php

use yii\db\Migration;

/**
 * Class m200925_163408_add_removed_to_feed_resource
 */
class m200925_163408_add_removed_to_feed_resource extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cap_resources', 'removed', $this->integer(1)->defaultValue(0));
        $this->addColumn('cap_resources', 'locked', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cap_resources', 'removed');
        $this->dropColumn('cap_resources', 'locked');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_163408_add_removed_to_feed_resource cannot be reverted.\n";

        return false;
    }
    */
}
