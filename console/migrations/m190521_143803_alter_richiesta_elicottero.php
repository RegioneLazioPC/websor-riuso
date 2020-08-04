<?php

use yii\db\Migration;

/**
 * Class m190521_143803_alter_richiesta_elicottero
 */
class m190521_143803_alter_richiesta_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_elicottero', 'id_elicottero', $this->integer());
        $this->addColumn('richiesta_elicottero', 'id_comune', $this->integer());
        $this->addColumn('richiesta_elicottero', 'dataora_decollo', $this->datetime());
        $this->addColumn('richiesta_elicottero', 'missione', $this->string());
        $this->addColumn('richiesta_elicottero', 'localita', $this->string());

        $this->addForeignKey(
            'fk-richiesta_elicottero_elicottero',
            'richiesta_elicottero',
            'id_elicottero',
            'utl_automezzo', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-richiesta_elicottero_comune',
            'richiesta_elicottero',
            'id_comune',
            'loc_comune', 
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
            'fk-richiesta_elicottero_elicottero',
            'richiesta_elicottero'
        );

        $this->dropForeignKey(
            'fk-richiesta_elicottero_comune',
            'richiesta_elicottero'
        );

        $this->dropColumn('richiesta_elicottero', 'id_elicottero');
        $this->dropColumn('richiesta_elicottero', 'id_comune');
        $this->dropColumn('richiesta_elicottero', 'dataora_decollo');
        $this->dropColumn('richiesta_elicottero', 'missione');
        $this->dropColumn('richiesta_elicottero', 'localita');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190521_143803_alter_richiesta_elicottero cannot be reverted.\n";

        return false;
    }
    */
}
