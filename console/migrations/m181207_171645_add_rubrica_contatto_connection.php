<?php

use yii\db\Migration;

/**
 * Class m181207_171645_add_rubrica_contatto_connection
 */
class m181207_171645_add_rubrica_contatto_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_mas_rubrica_contatto', [
            'id' => $this->primaryKey(),
            'id_contatto' => $this->integer(),
            'id_mas_rubrica' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-mas_rubrica_contatto_contatto',
            'con_mas_rubrica_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mas_rubrica_contatto_rubrica',
            'con_mas_rubrica_contatto',
            'id_mas_rubrica',
            'mas_rubrica',
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
            'fk-mas_rubrica_contatto_contatto',
            'con_mas_rubrica_contatto'
        );

        $this->dropForeignKey(
            'fk-mas_rubrica_contatto_rubrica',
            'con_mas_rubrica_contatto'
        );

        $this->dropTable("con_mas_rubrica_contatto");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181207_171645_add_rubrica_contatto_connection cannot be reverted.\n";

        return false;
    }
    */
}
