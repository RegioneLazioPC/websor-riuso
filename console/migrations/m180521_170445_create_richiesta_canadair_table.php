<?php

use yii\db\Migration;

/**
 * Handles the creation of table `richiesta_canadair`.
 */
class m180521_170445_create_richiesta_canadair_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('richiesta_canadair', [
            'id' => $this->primaryKey(),
            'idevento' => $this->integer(),
            'idoperatore' => $this->integer(),
            'idcomunicazione' => $this->integer(),
            'created_at' => $this->timestamp()->notNull()
        ]);

        $this->addForeignKey(
            'fk_richiesta_canadair_evento',
            'richiesta_canadair',
            'idevento',
            'utl_evento',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_richiesta_canadair_operatore',
            'richiesta_canadair',
            'idoperatore',
            'utl_operatore_pc',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_richiesta_canadair_comunicazione',
            'richiesta_canadair',
            'idcomunicazione',
            'com_comunicazioni',
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
            'fk_richiesta_canadair_evento',
            'richiesta_canadair'
        );

        $this->dropForeignKey(
            'fk_richiesta_canadair_operatore',
            'richiesta_canadair'
        );

        $this->dropForeignKey(
            'fk_richiesta_canadair_comunicazione',
            'richiesta_canadair'
        );

        $this->dropTable('richiesta_canadair');
    }
}
