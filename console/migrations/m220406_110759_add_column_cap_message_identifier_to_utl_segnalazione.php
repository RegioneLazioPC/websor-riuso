<?php

use yii\db\Migration;

/**
 * Class m220406_110759_add_column_cap_message_identifier_to_utl_segnalazione
 */
class m220406_110759_add_column_cap_message_identifier_to_utl_segnalazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_segnalazione', 'cap_message_identifier', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_segnalazione', 'cap_message_identifier');
    }
}
