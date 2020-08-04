<?php

use yii\db\Migration;

/**
 * Handles the creation of table `richiesta_dos`.
 */
class m180503_135507_create_richiesta_dos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('richiesta_dos', [
            'id' => $this->primaryKey(),
            'idevento' => $this->integer(),
            'idingaggio' => $this->integer(),
            'idoperatore' => $this->integer(),
            'idcomunicazione' => $this->integer(),
            'created_at' => $this->timestamp()->notNull()
        ]);

        $this->addForeignKey(
            'fk_richiesta_dos_evento',
            'richiesta_dos',
            'idevento',
            'utl_evento',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_richiesta_dos_ingaggio',
            'richiesta_dos',
            'idingaggio',
            'utl_ingaggio',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_richiesta_dos_operatore',
            'richiesta_dos',
            'idoperatore',
            'utl_operatore_pc',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_richiesta_dos_comunicazione',
            'richiesta_dos',
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
            'fk_richiesta_dos_evento',
            'richiesta_dos'
        );

        $this->dropForeignKey(
            'fk_richiesta_dos_ingaggio',
            'richiesta_dos'
        );

        $this->dropForeignKey(
            'fk_richiesta_dos_operatore',
            'richiesta_dos'
        );

        $this->dropForeignKey(
            'fk_richiesta_dos_comunicazione',
            'richiesta_dos'
        );

        $this->dropTable('richiesta_dos');
    }
}
