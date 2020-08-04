<?php

use yii\db\Migration;

/**
 * Class m181211_182359_alter_tables_for_rubrica_send
 */
class m181211_182359_alter_tables_for_rubrica_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_mas_invio_contact','channel',$this->string(20));
        $this->addColumn('con_mas_invio_contact','valore_rubrica_contatto',$this->string());

        $this->addColumn('mas_single_send','valore_rubrica_contatto',$this->string());

        $this->dropColumn('mas_single_send','channel');
        $this->dropColumn('mas_single_send','valore_contatto');
        $this->dropColumn('mas_single_send','tipo_rubrica_contatto');
        $this->addColumn('mas_single_send','tipo_rubrica_contatto',$this->string());
        $this->dropColumn('con_mas_invio_contact','tipo_rubrica_contatto');
        $this->addColumn('con_mas_invio_contact','tipo_rubrica_contatto',$this->string());
        $this->addColumn('mas_single_send','channel',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        

        $this->dropColumn('con_mas_invio_contact','channel');
        $this->dropColumn('con_mas_invio_contact','valore_rubrica_contatto');
        $this->dropColumn('mas_single_send','valore_rubrica_contatto');

        $this->dropColumn('mas_single_send','channel');
        $this->dropColumn('con_mas_invio_contact','tipo_rubrica_contatto');
        $this->addColumn('con_mas_invio_contact','tipo_rubrica_contatto',$this->string());
        $this->dropColumn('mas_single_send','tipo_rubrica_contatto');
        $this->addColumn('mas_single_send','tipo_rubrica_contatto',$this->string());
        $this->addColumn('mas_single_send','valore_contatto', $this->string());
        $this->addColumn('mas_single_send','channel',$this->integer());

        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181211_182359_alter_tables_for_rubrica_send cannot be reverted.\n";

        return false;
    }
    */
}
