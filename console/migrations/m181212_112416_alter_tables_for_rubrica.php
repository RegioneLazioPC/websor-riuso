<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181212_112416_alter_tables_for_rubrica
 */
class m181212_112416_alter_tables_for_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('mas_single_send','last_attempt');
        $this->dropColumn('mas_single_send','sending_attempts');
        $this->addColumn('mas_single_send','created_at',Schema::TYPE_INTEGER . ' NOT NULL');
        $this->addColumn('mas_single_send','updated_at',Schema::TYPE_INTEGER . ' NOT NULL');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('mas_single_send','last_attempt',$this->timestamp());
        $this->addColumn('mas_single_send','sending_attempts',$this->integer());
        $this->dropColumn('mas_single_send','created_at');
        $this->dropColumn('mas_single_send','updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181212_112416_alter_tables_for_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
