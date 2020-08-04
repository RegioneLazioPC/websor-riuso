<?php

use yii\db\Migration;

/**
 * Class m190205_085006_add_column_utl_utente_disabled
 */
class m190205_085006_add_column_utl_utente_disabled extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'utl_utente', 'enabled', $this->integer(1)->defaultValue(1) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'utl_utente', 'enabled' );
    }

}
