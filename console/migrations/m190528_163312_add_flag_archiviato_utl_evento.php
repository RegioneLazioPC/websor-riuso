<?php

use yii\db\Migration;

/**
 * Class m190528_163312_add_flag_archiviato_utl_evento
 */
class m190528_163312_add_flag_archiviato_utl_evento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_evento', 'archived', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_evento', 'archived');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190528_163312_add_flag_archiviato_utl_evento cannot be reverted.\n";

        return false;
    }
    */
}
