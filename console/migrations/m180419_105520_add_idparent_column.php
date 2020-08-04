<?php

use yii\db\Migration;

/**
 * Class m180419_105520_add_idparent_column
 */
class m180419_105520_add_idparent_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_evento', 'idparent', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_evento', 'idparent');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180419_105520_add_idparent_column cannot be reverted.\n";

        return false;
    }
    */
}
