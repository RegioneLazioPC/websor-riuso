<?php

use yii\db\Migration;

/**
 * Class m180415_160552_alter_organizzazione_fields_length
 */
class m180415_160552_alter_organizzazione_fields_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('vol_organizzazione', 'societa_assicurazione', $this->string(255));
        $this->alterColumn('vol_organizzazione', 'note', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('vol_organizzazione', 'societa_assicurazione', $this->string(1));
        $this->alterColumn('vol_organizzazione', 'note', $this->string(1));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180415_160552_alter_organizzazione_fields_length cannot be reverted.\n";

        return false;
    }
    */
}
