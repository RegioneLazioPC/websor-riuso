<?php

use yii\db\Migration;

/**
 * Class m210608_151156_add_device_id_elicottero
 */
class m210608_151156_add_device_id_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_automezzo', 'device_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_automezzo', 'device_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_151156_add_device_id_elicottero cannot be reverted.\n";

        return false;
    }
    */
}
