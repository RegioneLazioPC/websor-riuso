<?php

use yii\db\Migration;

/**
 * Class m210131_225753_add_comuni_gestione_update
 */
class m210131_225753_add_comuni_gestione_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("loc_comune", "soppresso", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("loc_comune", "soppresso");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210131_225753_add_comuni_gestione_update cannot be reverted.\n";

        return false;
    }
    */
}
