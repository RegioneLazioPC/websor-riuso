<?php

use yii\db\Migration;

/**
 * Class m210321_185222_add_dataorastimata_rich_elicottero
 */
class m210321_185222_add_dataorastimata_rich_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_elicottero', 'n_lanci', $this->integer());
        $this->addColumn('richiesta_elicottero', 'dataora_arrivo_stimato', $this->datetime());
        $this->addColumn('richiesta_elicottero', 'dataora_atterraggio', $this->datetime());
        $this->addColumn('richiesta_elicottero', 'deleted', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('richiesta_elicottero', 'n_lanci');
        $this->dropColumn('richiesta_elicottero', 'dataora_arrivo_stimato');
        $this->dropColumn('richiesta_elicottero', 'dataora_atterraggio');
        $this->dropColumn('richiesta_elicottero', 'deleted');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210321_185222_add_dataorastimata_rich_elicottero cannot be reverted.\n";

        return false;
    }
    */
}
