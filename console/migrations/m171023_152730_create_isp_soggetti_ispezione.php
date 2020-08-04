<?php

use yii\db\Migration;

class m171023_152730_create_isp_soggetti_ispezione extends Migration
{
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('isp_soggetti_ispezione');

        if ($tableSchema === null) {
            $this->createTable('isp_soggetti_ispezione', [
                'id' => $this->primaryKey(),
                'nome' => $this->string(255),
            ]);
        }
    }

    public function safeDown()
    {
        $this->dropTable('isp_soggetti_ispezione');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('isp_soggetti_ispezione', [
            'id' => $this->primaryKey(),
            'nome' => $this->integer(11),
        ]);
    }

    public function down()
    {
        $this->dropTable('isp_soggetti_ispezione');
    }
    */

}
