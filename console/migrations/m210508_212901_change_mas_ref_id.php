<?php

use yii\db\Migration;

/**
 * Class m210508_212901_change_mas_ref_id
 */
class m210508_212901_change_mas_ref_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('mas_message', 'mas_ref_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('mas_message', 'mas_ref_id', $this->string());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210508_212901_change_mas_ref_id cannot be reverted.\n";

        return false;
    }
    */
}
