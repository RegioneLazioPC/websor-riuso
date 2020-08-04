<?php

use yii\db\Migration;

/**
 * Class m180524_183445_add_rifiuto_attivazione_fields
 */
class m180524_183445_add_rifiuto_attivazione_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_ingaggio', 'motivazione_rifiuto', $this->integer());
        $this->addColumn('utl_ingaggio', 'motivazione_rifiuto_note', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_ingaggio', 'motivazione_rifiuto');
        $this->dropColumn('utl_ingaggio', 'motivazione_rifiuto_note');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180524_183445_add_rifiuto_attivazione_fields cannot be reverted.\n";

        return false;
    }
    */
}
