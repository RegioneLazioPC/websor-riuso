<?php

use yii\db\Migration;

/**
 * Class m181211_163643_add_contatto_value_to_mas_single_send
 */
class m181211_163643_add_contatto_value_to_mas_single_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_single_send', 'valore_contatto', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_single_send', 'valore_contatto');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181211_163643_add_contatto_value_to_mas_single_send cannot be reverted.\n";

        return false;
    }
    */
}
