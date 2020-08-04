<?php

use yii\db\Migration;

/**
 * Handles the creation of table `con_volontario`.
 */
class m180427_081439_create_con_volontario_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_volontario_ingaggio', [
            'id' => $this->primaryKey(),
            'id_volontario' => $this->integer(),
            'id_ingaggio' => $this->integer(),
        ]);

        $this->addColumn('vol_volontario', 'id_organizzazione', $this->integer());
        $this->addColumn('vol_volontario', 'id_sede', $this->integer());
        $this->addColumn('vol_volontario', 'id_user', $this->integer());

        $this->addForeignKey(
            'fk-con_volontario_ingaggio_volontario',
            'con_volontario_ingaggio',
            'id_volontario',
            'vol_volontario',
            'id'
        );

        $this->addForeignKey(
            'fk-con_volontario_ingaggio_ingaggio',
            'con_volontario_ingaggio',
            'id_ingaggio',
            'utl_ingaggio',
            'id'
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

        $this->addForeignKey(
            'fk-vol_volontario_user',
            'vol_volontario',
            'id_user',
            'user',
            'id'
        );

        $this->dropColumn('utl_operatore_pc', 'matricola');
        $this->addColumn('utl_anagrafica', 'matricola', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_anagrafica', 'matricola');
        $this->addColumn('utl_operatore_pc', 'matricola', $this->string(255));
        
        $this->dropForeignKey(
            'fk-con_volontario_ingaggio_volontario',
            'con_volontario_ingaggio'
        );

        $this->dropForeignKey(
            'fk-con_volontario_ingaggio_ingaggio',
            'con_volontario_ingaggio'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_organizzazione',
            'vol_volontario'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_sede',
            'vol_volontario'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_user',
            'vol_volontario'
        );

        $this->dropColumn('vol_volontario', 'id_organizzazione');
        $this->dropColumn('vol_volontario', 'id_sede');
        $this->dropColumn('vol_volontario', 'id_user');
        
        $this->dropTable('con_volontario_ingaggio');
    }
}
