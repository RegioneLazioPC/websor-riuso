<?php

use yii\db\Migration;

/**
 * Class m190118_173755_alter_mas_invio_status
 */
class m190118_173755_alter_mas_invio_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_invio', 'status', $this->integer(1)->defaultValue(0) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_invio', 'status' );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190118_173755_alter_mas_invio_status cannot be reverted.\n";

        return false;
    }
    */
}
