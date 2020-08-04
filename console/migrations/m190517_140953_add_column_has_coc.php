<?php

use yii\db\Migration;

/**
 * Class m190517_140953_add_column_has_coc
 */
class m190517_140953_add_column_has_coc extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_evento','has_coc', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_evento','has_coc');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_140953_add_column_has_coc cannot be reverted.\n";

        return false;
    }
    */
}
