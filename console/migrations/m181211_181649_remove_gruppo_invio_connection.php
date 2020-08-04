<?php

use yii\db\Migration;

/**
 * Class m181211_181649_remove_gruppo_invio_connection
 */
class m181211_181649_remove_gruppo_invio_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('con_mas_invio_rubrica_group');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('con_mas_invio_rubrica_group', [
            'id' => $this->primaryKey(),
            'id_invio' => $this->integer(),
            'id_group' => $this->integer()
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181211_181649_remove_gruppo_invio_connection cannot be reverted.\n";

        return false;
    }
    */
}
