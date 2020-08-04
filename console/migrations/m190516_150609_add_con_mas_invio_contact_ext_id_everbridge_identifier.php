<?php

use yii\db\Migration;

/**
 * Class m190516_150609_add_con_mas_invio_contact_ext_id_everbridge_identifier
 */
class m190516_150609_add_con_mas_invio_contact_ext_id_everbridge_identifier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_mas_invio_contact', 'ext_id', $this->string());
        $this->addColumn('con_mas_invio_contact', 'everbridge_identifier', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_mas_invio_contact', 'ext_id');
        $this->dropColumn('con_mas_invio_contact', 'everbridge_identifier');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_150609_add_con_mas_invio_contact_ext_id_everbridge_identifier cannot be reverted.\n";

        return false;
    }
    */
}
