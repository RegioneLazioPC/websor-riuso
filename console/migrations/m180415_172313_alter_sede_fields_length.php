<?php

use yii\db\Migration;

/**
 * Class m180415_172313_alter_sede_fields_length
 */
class m180415_172313_alter_sede_fields_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('vol_sede', 'indirizzo', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('vol_sede', 'indirizzo', $this->string(1));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180415_172313_alter_sede_fields_length cannot be reverted.\n";

        return false;
    }
    */
}
