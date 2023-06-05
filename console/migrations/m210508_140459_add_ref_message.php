<?php

use yii\db\Migration;

/**
 * Class m210508_140459_add_ref_message
 */
class m210508_140459_add_ref_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_message', 'mas_ref_id', $this->string());
        $this->addColumn('mas_invio', 'mas_ref_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_invio', 'mas_ref_id');
        $this->dropColumn('mas_message', 'mas_ref_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210508_140459_add_ref_message cannot be reverted.\n";

        return false;
    }
    */
}
