<?php

use yii\db\Migration;

/**
 * Class m190203_093512_alter_tables_for_mas
 */
class m190203_093512_alter_tables_for_mas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('mas_invio', 'status_mail');
        $this->dropColumn('mas_invio', 'status_pec');
        $this->dropColumn('mas_invio', 'status_sms');
        $this->dropColumn('mas_invio', 'status_fax');
        $this->dropColumn('mas_invio', 'status_push');
        $this->dropColumn('mas_invio', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('mas_invio', 'status_mail', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_pec', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_sms', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_fax', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status_push', $this->integer(1)->defaultValue(0));
        $this->addColumn('mas_invio', 'status', $this->integer(1)->defaultValue(0));

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190203_093512_alter_tables_for_mas cannot be reverted.\n";

        return false;
    }
    */
}
