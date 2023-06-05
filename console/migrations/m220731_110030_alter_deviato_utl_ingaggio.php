<?php

use yii\db\Migration;

/**
 * Class m220731_110030_alter_deviato_utl_ingaggio
 */
class m220731_110030_alter_deviato_utl_ingaggio extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('utl_ingaggio', 'deviato');
        $this->addColumn('utl_ingaggio', 'deviato', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_ingaggio', 'deviato');
        $this->addColumn('utl_ingaggio', 'deviato', $this->boolean()->defaultValue(false));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220731_110030_alter_deviato_utl_ingaggio cannot be reverted.\n";

        return false;
    }
    */
}
