<?php

use yii\db\Migration;

/**
 * Class m180604_094759_add_aggregatore_tipologie_table
 */
class m180604_094759_add_aggregatore_tipologie_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('utl_aggregatore_tipologie', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string()
        ]);

        $this->createTable('con_aggregatore_tipologie_tipologie', [
            'id' => $this->primaryKey(),
            'id_tipo_automezzo' => $this->integer(),
            'id_tipo_attrezzatura' => $this->integer(), 
            'id_aggregatore' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_con_aggregatore_tipologie_tipologie_tipo_automezzo',
            'con_aggregatore_tipologie_tipologie',
            'id_tipo_automezzo',
            'utl_automezzo_tipo',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_con_aggregatore_tipologie_tipologie_tipo_attrezzatura',
            'con_aggregatore_tipologie_tipologie',
            'id_tipo_attrezzatura',
            'utl_attrezzatura_tipo',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_con_aggregatore_tipologie_tipologie_aggregatore',
            'con_aggregatore_tipologie_tipologie',
            'id_aggregatore',
            'utl_aggregatore_tipologie',
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
            'fk_con_aggregatore_tipologie_tipologie_tipo_automezzo',
            'con_aggregatore_tipologie_tipologie'
        );

        $this->dropForeignKey(
            'fk_con_aggregatore_tipologie_tipologie_tipo_attrezzatura',
            'con_aggregatore_tipologie_tipologie'
        );

        $this->dropForeignKey(
            'fk_con_aggregatore_tipologie_tipologie_aggregatore',
            'con_aggregatore_tipologie_tipologie'
        );

        $this->dropTable('utl_aggregatore_tipologie');

        $this->dropTable('con_aggregatore_tipologie_tipologie');

        
    }

}
