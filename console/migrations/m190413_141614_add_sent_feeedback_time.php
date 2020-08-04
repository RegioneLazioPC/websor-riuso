<?php

use yii\db\Migration;
use yii\db\Schema;
/**
 * Class m190413_141614_add_sent_feeedback_time
 */
class m190413_141614_add_sent_feeedback_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('mas_single_send', 'sent_time', Schema::TYPE_INTEGER);
        $this->addColumn('mas_single_send', 'feedback_time', Schema::TYPE_INTEGER);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_single_send', 'sent_time');
        $this->dropColumn('mas_single_send', 'feedback_time');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190413_141614_add_sent_feeedback_time cannot be reverted.\n";

        return false;
    }
    */
}
