<?php

use yii\db\Migration;

/**
 * Class m190607_094702_add_id_message_mas
 */
class m190607_094702_add_id_message_mas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_single_send', 'id_mas_message', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_single_send', 'id_mas_message');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190607_094702_add_id_message_mas cannot be reverted.\n";

        return false;
    }
    */
}
