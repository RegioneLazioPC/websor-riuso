<?php

use yii\db\Migration;

/**
 * Class m220919_084155_remove_cascade_on_fkey
 */
class m220919_084155_remove_cascade_on_fkey extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-query_layer',
            'geo_query'
        );
        $this->addForeignKey(
            'fk-query_layer',
            'geo_query',
            'layer',
            'geo_layer', 
            'layer_name',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-query_layer',
            'geo_query'
        );
        $this->addForeignKey(
            'fk-query_layer',
            'geo_query',
            'layer',
            'geo_layer', 
            'layer_name',
            'CASCADE',
            'CASCADE'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220919_084155_remove_cascade_on_fkey cannot be reverted.\n";

        return false;
    }
    */
}
