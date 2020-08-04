<?php

use yii\db\Migration;

/**
 * Class m200226_101342_add_associazione_segnalazione_app_evento_table
 */
class m200226_101342_add_associazione_segnalazione_app_evento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_segnalazione_app_evento', [
            'id' => $this->primaryKey(),
            'id_segnalazione' => $this->integer(),
            'id_evento' => $this->integer(),
            'confirmed' => $this->integer()->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-con_segnalazione_app_evento_segnalazione',
            'con_segnalazione_app_evento',
            'id_evento',
            'utl_evento', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_segnalazione_app_evento_evento',
            'con_segnalazione_app_evento',
            'id_segnalazione',
            'utl_segnalazione', 
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
            'fk-con_segnalazione_app_evento_segnalazione',
            'con_segnalazione_app_evento'
        );

        $this->dropForeignKey(
            'fk-con_segnalazione_app_evento_evento',
            'con_segnalazione_app_evento'
        );

        $this->dropTable('con_segnalazione_app_evento');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_101342_add_associazione_segnalazione_app_evento_table cannot be reverted.\n";

        return false;
    }
    */
}
