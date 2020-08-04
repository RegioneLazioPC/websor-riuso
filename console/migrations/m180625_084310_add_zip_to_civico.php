<?php

use yii\db\Migration;

/**
 * Class m180625_084310_add_zip_to_civico
 */
class m180625_084310_add_zip_to_civico extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('loc_civico', 'cap', $this->string(5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('loc_civico', 'cap');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180625_084310_add_zip_to_civico cannot be reverted.\n";

        return false;
    }
    */
}
