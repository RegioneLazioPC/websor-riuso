<?php

use yii\db\Migration;

/**
 * Class m181115_175915_add_sync_columns
 */
class m181115_175915_sync_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("vol_organizzazione","id_sync",$this->string());
        $this->addColumn("vol_organizzazione","cf_rappresentante_legale",$this->string());
        $this->addColumn("vol_organizzazione","cf_referente",$this->string());

        $this->addColumn("vol_sede","id_sync",$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("vol_organizzazione","id_sync");
        $this->dropColumn("vol_organizzazione","cf_rappresentante_legale");
        $this->dropColumn("vol_organizzazione","cf_referente");

        $this->dropColumn("vol_sede","id_sync");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181115_175915_add_sync_columns cannot be reverted.\n";

        return false;
    }
    */
}
