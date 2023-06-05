<?php

use yii\db\Migration;

/**
 * Class m230330_115626_add_auth_item_administrative
 */
class m230330_115626_add_auth_item_administrative extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->addColumn('auth_item', 'administrative', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('auth_item', 'administrative');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230330_115626_add_auth_item_administrative cannot be reverted.\n";

        return false;
    }
    */
}
