<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m220915_113024_geo_query_management
 */
class m220915_113024_geo_query_management extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('geo_query', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000)->unique(),
            'layer' => $this->string(59),
            'group' => $this->string(),
            'query_type' => $this->string(),
            'buffer' => $this->integer(), // proiettare in 32632
            'n_geometries' => $this->integer(), 
            'result_type' => $this->string(),
            'show_distance' => $this->boolean(),
            'layer_return_field' => $this->string(),
            'result_position' => $this->integer(),
            'enabled' => $this->boolean(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-query_layer',
            'geo_query'
        );

        $this->dropTable('geo_query');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220915_113024_geo_query_management cannot be reverted.\n";

        return false;
    }
    */
}
