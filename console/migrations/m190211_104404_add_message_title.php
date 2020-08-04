<?php

use yii\db\Migration;

/**
 * Class m190211_104404_add_message_title
 */
class m190211_104404_add_message_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_message', 'title', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_message', 'title');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190211_104404_add_message_title cannot be reverted.\n";

        return false;
    }
    */
}
