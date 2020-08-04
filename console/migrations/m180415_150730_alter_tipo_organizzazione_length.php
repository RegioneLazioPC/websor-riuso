<?php

use yii\db\Migration;

/**
 * Class m180415_150730_alter_tipo_organizzazione_length
 */
class m180415_150730_alter_tipo_organizzazione_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('vol_tipo_organizzazione', 'tipologia', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('vol_tipo_organizzazione', 'tipologia', $this->string(1));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180415_150730_alter_tipo_organizzazione_length cannot be reverted.\n";

        return false;
    }
    */
}
