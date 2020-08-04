<?php

use yii\db\Migration;

/**
 * Class m180509_155509_add_anagrafica_other_info
 */
class m180509_155509_add_anagrafica_other_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'utl_anagrafica', 'indirizzo_residenza', $this->string() );
        $this->addColumn( 'utl_anagrafica', 'cap_residenza', $this->string(5) );
        $this->addColumn( 'utl_anagrafica', 'pec', $this->string() );
        $this->alterColumn( 'utl_anagrafica', 'telefono', $this->string(100) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'utl_anagrafica', 'indirizzo_residenza' );
        $this->dropColumn( 'utl_anagrafica', 'cap_residenza' );
        $this->dropColumn( 'utl_anagrafica', 'pec' );
        $this->alterColumn( 'utl_anagrafica', 'telefono', $this->string(100) );
    }

}