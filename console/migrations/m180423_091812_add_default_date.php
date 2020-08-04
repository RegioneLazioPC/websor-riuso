<?php

use yii\db\Migration;

/**
 * Class m180423_091812_add_default_date
 */
class m180423_091812_add_default_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('utl_utente', 'data_nascita');
        $this->addColumn('utl_utente', 'data_nascita', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_utente', 'data_nascita');
        $this->addColumn('utl_utente', 'data_nascita', $this->timestamp());
    }

}
