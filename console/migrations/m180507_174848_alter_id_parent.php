<?php

use yii\db\Migration;

/**
 * Class m180507_174848_alter_id_parent
 */
class m180507_174848_alter_id_parent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('utl_evento', 'idparent', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('utl_evento', 'idparent', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180507_174848_alter_id_parent cannot be reverted.\n";

        return false;
    }
    */
}
