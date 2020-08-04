<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vol_sede_specializzazione`.
 */
class m180504_142601_create_vol_sede_specializzazione_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('utl_specializzazione', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->text()
        ]);

        $this->addColumn('vol_sede', 'id_specializzazione', $this->integer());

        $this->addForeignKey(
            'fk_vol_sede_specializzazione',
            'vol_sede',
            'id_specializzazione',
            'utl_specializzazione',
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
            'fk_vol_sede_specializzazione',
            'vol_sede'
        );
        $this->dropColumn('vol_sede', 'id_specializzazione');
        $this->dropTable('utl_specializzazione');
    }
}
