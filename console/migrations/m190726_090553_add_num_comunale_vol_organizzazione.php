<?php

use yii\db\Migration;

/**
 * Class m190726_090553_add_num_comunale_vol_organizzazione
 */
class m190726_090553_add_num_comunale_vol_organizzazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vol_organizzazione', 'num_comunale', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_organizzazione', 'num_comunale');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190726_090553_add_num_comunale_vol_organizzazione cannot be reverted.\n";

        return false;
    }
    */
}
