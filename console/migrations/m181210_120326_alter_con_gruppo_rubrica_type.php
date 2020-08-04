<?php

use yii\db\Migration;

/**
 * Class m181210_120326_alter_con_gruppo_rubrica_type
 */
class m181210_120326_alter_con_gruppo_rubrica_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('con_rubrica_group_contact', 'tipo_rubrica_contatto');
        $this->addColumn('con_rubrica_group_contact', 'tipo_rubrica_contatto', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_rubrica_group_contact', 'tipo_rubrica_contatto');
        $this->addColumn('con_rubrica_group_contact', 'tipo_rubrica_contatto', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181210_120326_alter_con_gruppo_rubrica_type cannot be reverted.\n";

        return false;
    }
    */
}
