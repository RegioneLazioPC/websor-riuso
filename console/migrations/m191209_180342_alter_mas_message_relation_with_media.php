<?php

use yii\db\Migration;

/**
 * Class m191209_180342_alter_mas_message_relation_with_media
 */
class m191209_180342_alter_mas_message_relation_with_media extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_mas_message_media', [
            'id' => $this->primaryKey(),
            'id_mas_message' => $this->integer(),
            'id_media' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-con_mas_message_media_media',
            'con_mas_message_media',
            'id_media',
            'upl_media', 
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-con_mas_message_media_message',
            'con_mas_message_media',
            'id_mas_message',
            'mas_message', 
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
            'fk-con_mas_message_media_media',
            'con_mas_message_media'
        );

        $this->dropForeignKey(
            'fk-con_mas_message_media_message',
            'con_mas_message_media'
        );

        $this->dropTable('con_mas_message_media');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_180342_alter_mas_message_relation_with_media cannot be reverted.\n";

        return false;
    }
    */
}
