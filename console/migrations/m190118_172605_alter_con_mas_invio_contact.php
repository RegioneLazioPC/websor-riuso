<?php

use yii\db\Migration;

/**
 * Class m190118_172605_alter_con_mas_invio_contact
 */
class m190118_172605_alter_con_mas_invio_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_mas_invio_contact', 'vendor', $this->string(10)->defaultValue(''));
        $this->addColumn('con_mas_invio_contact', 'valore_riferimento', $this->string(255)->defaultValue(''));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_mas_invio_contact', 'vendor');
        $this->dropColumn('con_mas_invio_contact', 'valore_riferimento');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190118_172605_alter_con_mas_invio_contact cannot be reverted.\n";

        return false;
    }
    */
}
