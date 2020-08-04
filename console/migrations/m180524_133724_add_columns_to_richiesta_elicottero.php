<?php

use yii\db\Migration;

/**
 * Class m180524_133724_add_columns_to_richiesta_elicottero
 */
class m180524_133724_add_columns_to_richiesta_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_elicottero', 'engaged', $this->boolean()->defaultValue(false));
        $this->addColumn('richiesta_elicottero', 'codice_elicottero', $this->string(100));
        $this->addColumn('richiesta_elicottero', 'motivo_rifiuto', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'richiesta_elicottero', 'engaged' );
        $this->dropColumn( 'richiesta_elicottero', 'codice_elicottero' );
        $this->dropColumn( 'richiesta_elicottero', 'motivo_rifiuto' );
    }
}
