<?php

use yii\db\Migration;

/**
 * Class m190615_180459_add_cap_ente_struttura
 */
class m190615_180459_add_cap_ente_struttura extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ent_ente_sede', 'cap', $this->string(5));
        $this->addColumn('str_struttura_sede', 'cap', $this->string(5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ent_ente_sede', 'cap');
        $this->dropColumn('str_struttura_sede', 'cap');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190615_180459_add_cap_ente_struttura cannot be reverted.\n";

        return false;
    }
    */
}
