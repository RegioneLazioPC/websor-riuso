<?php

use yii\db\Migration;

/**
 * Class m181214_102148_manage_predefinito_and_mobile_on_contatto
 */
class m181214_102148_manage_predefinito_and_mobile_on_contatto extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_contatto', 'check_predefinito', $this->integer(1)->defaultValue(1));
        $this->addColumn('utl_contatto', 'check_mobile', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_contatto', 'check_predefinito');
        $this->dropColumn('utl_contatto', 'check_mobile');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181214_102148_manage_predefinito_and_mobile_on_contatto cannot be reverted.\n";

        return false;
    }
    */
}
