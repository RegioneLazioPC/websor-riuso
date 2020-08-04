<?php

use yii\db\Migration;

/**
 * Class m190413_131258_add_created_by_mas_message
 */
class m190413_131258_add_created_by_mas_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_message', 'id_user', $this->integer());
        $this->addColumn('mas_invio', 'id_user', $this->integer());

        $this->addForeignKey(
            'fk-mas_message_creator',
            'mas_message',
            'id_user',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mas_invio_creator',
            'mas_invio',
            'id_user',
            'user',
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
            'fk-mas_message_creator',
            'mas_message'
        );

        $this->dropForeignKey(
            'fk-mas_invio_creator',
            'mas_invio'
        );

        $this->dropColumn('mas_message', 'id_user');
        $this->dropColumn('mas_invio', 'id_user');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190413_131258_add_created_by_mas_message cannot be reverted.\n";

        return false;
    }
    */
}
