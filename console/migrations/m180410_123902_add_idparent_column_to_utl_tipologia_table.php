<?php

use yii\db\Migration;

/**
 * Handles adding idparent to table `utl_tipologia`.
 */
class m180410_123902_add_idparent_column_to_utl_tipologia_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_tipologia', 'idparent', $this->integer()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_tipologia', 'idparent');
    }
}
