<?php

use yii\db\Migration;

/**
 * Class m180419_161922_add_utl_ingaggi_table
 */
class m180419_161922_add_utl_ingaggi_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('utl_ingaggio', [
            'id' => $this->primaryKey(),
            'idevento' => $this->integer(),
            'idorganizzazione' => $this->integer(),
            'idsede' => $this->integer(),
            'idautomezzo' => $this->integer(),
            'idattrezzatura' => $this->integer(),
            'note' => $this->text(),
            'stato' => $this->integer(), // 0 -> 'in attesa di conferma', 1 => 'confermato', 2 => 'rifiutato', 3 => 'chiuso'
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'closed_at' => $this->timestamp()
        ]);

        $this->addForeignKey(
            'fk_utl_ingaggio_utl_evento',
            'utl_ingaggio',
            'idevento',
            'utl_evento',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_ingaggio_vol_organizzazione',
            'utl_ingaggio',
            'idorganizzazione',
            'vol_organizzazione',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_ingaggio_vol_sede',
            'utl_ingaggio',
            'idsede',
            'vol_sede',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_ingaggio_utl_automezzo',
            'utl_ingaggio',
            'idautomezzo',
            'utl_automezzo',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_ingaggio_utl_attrezzatura',
            'utl_ingaggio',
            'idattrezzatura',
            'utl_attrezzatura',
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
            'fk_utl_ingaggio_utl_evento',
            'utl_ingaggio'
        );

        $this->dropForeignKey(
            'fk_utl_ingaggio_vol_organizzazione',
            'utl_ingaggio'
        );

        $this->dropForeignKey(
            'fk_utl_ingaggio_vol_sede',
            'utl_ingaggio'
        );

        $this->dropForeignKey(
            'fk_utl_ingaggio_utl_automezzo',
            'utl_ingaggio'
        );

        $this->dropForeignKey(
            'fk_utl_ingaggio_utl_attrezzatura',
            'utl_ingaggio'
        );

        $this->dropTable('utl_ingaggio');
    }

}
