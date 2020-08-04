<?php

use yii\db\Migration;

class m171023_153808_create_junction_table_for_isp_ispezione_isp_soggetti_ispezione_tables extends Migration
{
//    public function safeUp()
//    {
//
//    }
//
//    public function safeDown()
//    {
//        echo "m171023_153808_create_junction_table_for_isp_ispezione_isp_soggetti_ispezione_tables cannot be reverted.\n";
//
//        return false;
//    }


    // Use up()/down() to run migration code without a transaction.
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('con_ispezione_soggetti');

        if ($tableSchema === null) {
            $this->createTable('con_ispezione_soggetti', [
                'idispezione' => $this->integer(),
                'idsoggetto' => $this->integer(),
                'created_at' => $this->dateTime(),
                'PRIMARY KEY(idispezione, idsoggetto)',
            ]);

            // creates index for column `post_id`
            $this->createIndex(
                'idx-ispezione_id',
                'con_ispezione_soggetti',
                'idispezione'
            );

            // add foreign key for table `post`
            $this->addForeignKey(
                'fk-ispezione_id',
                'con_ispezione_soggetti',
                'idispezione',
                'isp_ispezione',
                'id',
                'CASCADE'
            );

            // creates index for column `tag_id`
            $this->createIndex(
                'idx-soggetto_id',
                'con_ispezione_soggetti',
                'idsoggetto'
            );

            // add foreign key for table `tag`
            $this->addForeignKey(
                'fk-soggetto_id',
                'con_ispezione_soggetti',
                'idsoggetto',
                'isp_soggetti_ispezione',
                'id',
                'CASCADE'
            );
        }
    }

    public function safeDown()
    {
        

            
            $this->dropIndex(
                'idx-ispezione_id',
                'con_ispezione_soggetti');

            
            $this->dropForeignKey(
                'fk-ispezione_id',
                'con_ispezione_soggetti');

            
            $this->dropIndex(
                'idx-soggetto_id',
                'con_ispezione_soggetti');

            
            $this->dropForeignKey(
                'fk-soggetto_id',
                'con_ispezione_soggetti');

            $this->dropTable('con_ispezione_soggetti');
    }

}
