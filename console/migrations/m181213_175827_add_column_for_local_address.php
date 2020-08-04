<?php

use yii\db\Migration;

/**
 * Class m181213_175827_add_column_for_local_address
 */
class m181213_175827_add_column_for_local_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_segnalazione','address_type',$this->integer(1)->defaultValue(3));
        $this->addColumn('utl_segnalazione','id_indirizzo',$this->integer());
        $this->addColumn('utl_segnalazione','id_civico',$this->integer());

        $this->addColumn('utl_evento','address_type',$this->integer(1)->defaultValue(3));
        $this->addColumn('utl_evento','id_indirizzo',$this->integer());
        $this->addColumn('utl_evento','id_civico',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_segnalazione','address_type');
        $this->dropColumn('utl_segnalazione','id_indirizzo');
        $this->dropColumn('utl_segnalazione','id_civico');

        $this->dropColumn('utl_evento','address_type');
        $this->dropColumn('utl_evento','id_indirizzo');
        $this->dropColumn('utl_evento','id_civico');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181213_175827_add_column_for_local_address cannot be reverted.\n";

        return false;
    }
    */
}
