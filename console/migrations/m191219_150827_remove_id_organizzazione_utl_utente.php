<?php

use yii\db\Migration;

/**
 * Class m191219_150827_remove_id_organizzazione_utl_utente
 */
class m191219_150827_remove_id_organizzazione_utl_utente extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-utl_utente_organizzazione',
            'utl_utente'
        );
        $this->dropColumn('utl_utente', 'id_organizzazione');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('utl_utente', 'id_organizzazione', $this->integer());
        $this->addForeignKey(
            'fk-utl_utente_organizzazione',
            'utl_utente',
            'id_organizzazione',
            'vol_organizzazione', 
            'id',
            'SET NULL'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191219_150827_remove_id_organizzazione_utl_utente cannot be reverted.\n";

        return false;
    }
    */
}
