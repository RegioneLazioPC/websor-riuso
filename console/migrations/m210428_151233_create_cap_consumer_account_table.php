<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `cap_consumer_account`.
 */
class m210428_151233_create_cap_consumer_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cap_consumer', [
            'id' => $this->primaryKey(),
            'enabled' => $this->integer(1),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'address' => $this->string(),
            'comuni' => $this->json(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        Yii::$app->db->createCommand("SELECT AddGeometryColumn ('cap_consumer','geom', 32632, 'MULTIPOLYGON', 2);")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cap_consumer');
    }
}
