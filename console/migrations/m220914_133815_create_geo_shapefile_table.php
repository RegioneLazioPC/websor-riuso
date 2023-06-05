<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%geo_shapefile}}`.
 */
class m220914_133815_create_geo_shapefile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('geo_layer', [
            'id' => $this->primaryKey(),
            'layer_name' => $this->string(59)->unique(),
            'shapefile_name' => 'varchar',
            'fields' => $this->json(),
            'table_name' => $this->string(),
            'geometry_column' => $this->string(),
            'geometry_type' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        try {
            Yii::$app->db->createCommand("CREATE SCHEMA IF NOT EXISTS " . Yii::$app->params['geo_layer'])->execute();
        } catch(\Exception $e) {
            throw new \Exception("CONTROLLA DI AVERE I PERMESSI PER CREARE UNO SCHEMA SUL DATABASE");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('geo_layer');
    }
}
