<?php

use yii\db\Migration;

/**
 * Class m191210_085923_change_allerta_media_to_connection
 */
class m191210_085923_change_allerta_media_to_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_alm_allerta_media', [
            'id' => $this->primaryKey(),
            'id_allerta' => $this->integer(),
            'id_media' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-alm_allerta_media_media',
            'con_alm_allerta_media',
            'id_media',
            'upl_media', 
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-alm_allerta_media_message',
            'con_alm_allerta_media',
            'id_allerta',
            'alm_allerta_meteo', 
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-alm_allerta_media_media',
            'con_alm_allerta_media'
        );

        $this->dropForeignKey(
            'fk-alm_allerta_media_message',
            'con_alm_allerta_media'
        );

        $this->dropTable('con_alm_allerta_media');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191210_085923_change_allerta_media_to_connection cannot be reverted.\n";

        return false;
    }
    */
}
