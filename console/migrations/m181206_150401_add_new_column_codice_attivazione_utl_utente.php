<?php

use yii\db\Migration;

/**
 * Class m181206_150401_add_new_column_codice_attivazione_utl_utente
 */
class m181206_150401_add_new_column_codice_attivazione_utl_utente extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_utente', 'codice_attivazione', $this->string());
        $this->addColumn('utl_utente', 'telefono', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_utente', 'codice_attivazione');
        $this->dropColumn('utl_utente', 'telefono');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181206_150401_add_new_column_codice_attivazione_utl_utente cannot be reverted.\n";

        return false;
    }
    */
}
