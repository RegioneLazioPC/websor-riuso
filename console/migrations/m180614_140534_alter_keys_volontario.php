<?php

use yii\db\Migration;

/**
 * Class m180614_140534_alter_keys_volontario
 */
class m180614_140534_alter_keys_volontario extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-vol_volontario_organizzazione',
            'vol_volontario'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_sede',
            'vol_volontario'
        );

        $this->addForeignKey(
            'fk-vol_volontario_organizzazione',
            'vol_volontario',
            'id_organizzazione',
            'vol_organizzazione',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-vol_volontario_sede',
            'vol_volontario',
            'id_sede',
            'vol_sede',
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
            'fk-vol_volontario_organizzazione',
            'vol_volontario'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_sede',
            'vol_volontario'
        );

        $this->addForeignKey(
            'fk-vol_volontario_organizzazione',
            'vol_volontario',
            'id_organizzazione',
            'vol_organizzazione',
            'id'
        );

        $this->addForeignKey(
            'fk-vol_volontario_sede',
            'vol_volontario',
            'id_sede',
            'vol_sede',
            'id'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180614_140534_alter_keys_volontario cannot be reverted.\n";

        return false;
    }
    */
}
