<?php

use yii\db\Migration;

/**
 * Handles the creation of table `addresses`.
 */
class m180623_171758_create_addresses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('loc_indirizzo', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'id_comune' => $this->integer()
        ]);

        $this->createTable('loc_civico', [
            'id' => $this->primaryKey(),
            'civico' => $this->string(),
            'id_indirizzo' => $this->integer(),
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5)
        ]);

        $this->addForeignKey(
            'fk-loc_indirizzo_comune',
            'loc_indirizzo',
            'id_comune',
            'loc_comune',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-loc_civico_indirizzo',
            'loc_civico',
            'id_indirizzo',
            'loc_indirizzo',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('loc_indirizzo');
        $this->dropTable('loc_civico');
    }
}
