<?php

use yii\db\Migration;

/**
 * Class m200925_112332_add_ref_cap_segnalazione
 */
class m200925_112332_add_ref_cap_segnalazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_segnalazione', 'id_cap_message', $this->integer());

        $this->addForeignKey(
            'fk-segnalazione_cap',
            'utl_segnalazione',
            'id_cap_message',
            'cap_messages', 
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-segnalazione_cap',
            'utl_segnalazione'
        );
        $this->dropColumn('utl_segnalazione', 'id_cap_message');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_112332_add_ref_cap_segnalazione cannot be reverted.\n";

        return false;
    }
    */
}
