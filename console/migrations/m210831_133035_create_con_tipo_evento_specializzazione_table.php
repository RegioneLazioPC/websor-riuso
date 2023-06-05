<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%con_tipo_evento_specializzazione}}`.
 */
class m210831_133035_create_con_tipo_evento_specializzazione_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_tipo_evento_specializzazione', [
            'id' => $this->primaryKey(),
            'id_utl_tipologia' => $this->integer(),
            'id_tbl_sezione_specialistica' => $this->integer(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        $this->addForeignKey(
            'fk-tipo_evento_spec_evt',
            'con_tipo_evento_specializzazione',
            'id_utl_tipologia',
            'utl_tipologia', 
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-tipo_evento_spec_spec',
            'con_tipo_evento_specializzazione',
            'id_tbl_sezione_specialistica',
            'tbl_sezione_specialistica', 
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
            'fk-tipo_evento_spec_evt',
            'con_tipo_evento_specializzazione'
        );
        $this->dropForeignKey(
            'fk-tipo_evento_spec_spec',
            'con_tipo_evento_specializzazione'
        );
        $this->dropTable('con_tipo_evento_specializzazione');
    }
}
