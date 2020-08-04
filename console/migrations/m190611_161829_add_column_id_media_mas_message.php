<?php

use yii\db\Migration;

/**
 * Class m190611_161829_add_column_id_media_mas_message
 */
class m190611_161829_add_column_id_media_mas_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_message', 'id_media', $this->integer());

        $this->addForeignKey(
            'fk-mas_message_id_media',
            'mas_message',
            'id_media',
            'upl_media', 
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-mas_message_id_media',
            'mas_message'
        );
        $this->dropColumn('mas_message', 'id_media');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190611_161829_add_column_id_media_mas_message cannot be reverted.\n";

        return false;
    }
    */
}
