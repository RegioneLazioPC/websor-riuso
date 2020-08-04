<?php

use yii\db\Migration;

/**
 * Class m181116_174127_sync_other_columns
 */
class m181116_174127_sync_other_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("utl_specializzazione","id_sync",$this->string());
        $this->addColumn("tbl_sezione_specialistica","id_sync",$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("tbl_sezione_specialistica","id_sync");
        $this->dropColumn("utl_specializzazione","id_sync");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181116_174127_sync_other_columns cannot be reverted.\n";

        return false;
    }
    */
}
