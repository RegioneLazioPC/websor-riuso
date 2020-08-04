<?php

use yii\db\Migration;

/**
 * Class m181212_161625_add_mas_single_send_ref_to_con_invio_contact
 */
class m181212_161625_add_mas_single_send_ref_to_con_invio_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_single_send','id_con_mas_invio_contact', $this->integer());
        $this->addForeignKey(
            'fk-single_send_con_invio_cont',
            'mas_single_send',
            'id_con_mas_invio_contact',
            'con_mas_invio_contact',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-single_send_con_invio_cont',
            'mas_single_send'
        );
        $this->dropColumn('mas_single_send','id_con_mas_invio_contact');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181212_161625_add_mas_single_send_ref_to_con_invio_contact cannot be reverted.\n";

        return false;
    }
    */
}
