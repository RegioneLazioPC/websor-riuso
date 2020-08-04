<?php

use yii\db\Migration;

/**
 * Class m190626_104918_add_column_anagrafica_funzionario_richiesta_elicottero
 */
class m190626_104918_add_column_anagrafica_funzionario_richiesta_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_elicottero', 'id_anagrafica_funzionario', $this->integer());

        $this->addForeignKey(
            'fk-richiesta_elicottero_anagrafica_funzionario',
            'richiesta_elicottero',
            'id_anagrafica_funzionario',
            'utl_anagrafica', 
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
            'fk-richiesta_elicottero_anagrafica_funzionario',
            'richiesta_elicottero'
        );
        $this->dropColumn('richiesta_elicottero', 'id_anagrafica_funzionario');
    }

}
