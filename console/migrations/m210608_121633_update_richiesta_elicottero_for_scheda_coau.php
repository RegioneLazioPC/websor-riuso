<?php

use yii\db\Migration;

/**
 * Class m210608_121633_update_richiesta_elicottero_for_scheda_coau
 */
class m210608_121633_update_richiesta_elicottero_for_scheda_coau extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'richiesta_elicottero', 'deviato', $this->integer(1)->defaultValue(0) );
        $this->addColumn( 'richiesta_elicottero', 'dos', $this->integer(1)->defaultValue(0) );
        $this->addColumn( 'richiesta_elicottero', 'squadre_volontariato', $this->integer(1)->defaultValue(0) );
        $this->addColumn( 'richiesta_elicottero', 'squadre_vvf', $this->integer(1)->defaultValue(0) );
        $this->addColumn( 'richiesta_elicottero', 'id_tipo_evento', $this->integer() );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'richiesta_elicottero', 'deviato' );
        $this->dropColumn( 'richiesta_elicottero', 'dos' );
        $this->dropColumn( 'richiesta_elicottero', 'squadre_volontariato' );
        $this->dropColumn( 'richiesta_elicottero', 'squadre_vvf' );
        $this->dropColumn( 'richiesta_elicottero', 'id_tipo_evento' );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_121633_update_richiesta_elicottero_for_scheda_coau cannot be reverted.\n";

        return false;
    }
    */
}
