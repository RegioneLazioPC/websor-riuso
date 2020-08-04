<?php

use yii\db\Migration;

/**
 * Handles adding dataoramissione to table `richiesta_mezzo_aereo`.
 */
class m180504_080945_add_dataoramissione_column_to_richiesta_mezzo_aereo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_mezzo_aereo', 'dataora_inizio_missione', $this->timestamp());
        $this->addColumn('richiesta_mezzo_aereo', 'dataora_fine_missione', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('richiesta_mezzo_aereo', 'dataora_inizio_missione');
        $this->dropColumn('richiesta_mezzo_aereo', 'dataora_fine_missione');
    }
}
