<?php

use yii\db\Migration;

/**
 * Class m180405_161810_alter_frequenza_tras
 */
class m180405_161810_alter_frequenza_tras extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('utl_squadra_operativa','frequenza_trans');
        $this->addColumn('utl_squadra_operativa','frequenza_tras', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_squadra_operativa','frequenza_tras');
        $this->addColumn('utl_squadra_operativa','frequenza_trans', $this->string(255));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180405_161810_alter_frequenza_tras cannot be reverted.\n";

        return false;
    }
    */
}
