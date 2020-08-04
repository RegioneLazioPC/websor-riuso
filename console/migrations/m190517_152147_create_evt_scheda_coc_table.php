<?php

use yii\db\Migration;

/**
 * Handles the creation of table `evt_scheda_coc`.
 */
class m190517_152147_create_evt_scheda_coc_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('evt_scheda_coc', [
            'id' => $this->primaryKey(),
            'id_evento' => $this->integer(),
            'data_apertura' => $this->datetime(),
            'data_chiusura' => $this->datetime(),
            'num_atto' => $this->string(),
            'note' => $this->text()
        ]);

        $this->createTable('con_scheda_coc_documenti', [
            'id' => $this->primaryKey(),
            'id_scheda_coc' => $this->integer(),
            'id_upl_media' => $this->integer(),
            'note' => $this->text()
        ]);


        $this->addForeignKey(
            'fk-evt_scheda_coc_evento',
            'evt_scheda_coc',
            'id_evento',
            'utl_evento', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_scheda_coc_documenti_scheda',
            'con_scheda_coc_documenti',
            'id_scheda_coc',
            'evt_scheda_coc', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_scheda_coc_documenti_media',
            'con_scheda_coc_documenti',
            'id_upl_media',
            'upl_media', 
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
            'fk-evt_scheda_coc_evento',
            'evt_scheda_coc'
        );

        $this->dropForeignKey(
            'fk-con_scheda_coc_documenti_scheda',
            'con_scheda_coc_documenti'
        );

        $this->dropForeignKey(
            'fk-con_scheda_coc_documenti_media',
            'con_scheda_coc_documenti'
        );

        $this->dropTable('con_scheda_coc_documenti');
        $this->dropTable('evt_scheda_coc');
    }
}
