<?php

use yii\db\Migration;
use yii\db\Schema;
/**
 * Class m190413_132404_add_created_at_updated_at_mas_invio
 */
class m190413_132404_add_created_at_updated_at_mas_invio extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_invio', 'created_at', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT extract(epoch from now())');
        $this->addColumn('mas_invio', 'updated_at', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT extract(epoch from now())');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_invio', 'created_at');
        $this->dropColumn('mas_invio', 'updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190413_132404_add_created_at_updated_at_mas_invio cannot be reverted.\n";

        return false;
    }
    */
}
