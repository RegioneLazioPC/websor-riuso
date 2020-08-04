<?php

use yii\db\Migration;

/**
 * Class m190510_065455_alter_views_for_rubrica
 */
class m190510_065455_alter_views_for_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        


        $this->createTable('con_operatore_pc_contatto', [
            'id' => $this->primaryKey(),
            'id_operatore_pc' => $this->integer(),
            'id_contatto' => $this->integer(),
            'use_type' => $this->integer(1)->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-con_operatore_pc_contatto_contatto',
            'con_operatore_pc_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_operatore_pc_contatto_operatore',
            'con_operatore_pc_contatto',
            'id_operatore_pc',
            'utl_operatore_pc',
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
            'fk-con_operatore_pc_contatto_contatto',
            'con_operatore_pc_contatto'
        );

        $this->dropForeignKey(
            'fk-con_operatore_pc_contatto_operatore',
            'con_operatore_pc_contatto'
        );

        $this->dropTable('con_operatore_pc_contatto');

        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_065455_alter_views_for_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
