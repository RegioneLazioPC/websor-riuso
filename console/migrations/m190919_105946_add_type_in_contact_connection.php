<?php

use yii\db\Migration;

/**
 * Class m190919_105946_add_type_in_contact_connection
 */
class m190919_105946_add_type_in_contact_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_anagrafica_contatto', 'type', $this->integer(1));
        
        $this->addColumn('con_mas_rubrica_contatto', 'type', $this->integer(1));
        
        $this->addColumn('con_operatore_pc_contatto', 'type', $this->integer(1));
        
        $this->addColumn('con_organizzazione_contatto', 'type', $this->integer(1));
        $this->addColumn('con_sede_contatto', 'type', $this->integer(1));

        $this->addColumn('con_ente_contatto', 'type', $this->integer(1));
        $this->addColumn('con_ente_sede_contatto', 'type', $this->integer(1));

        $this->addColumn('con_struttura_contatto', 'type', $this->integer(1));
        $this->addColumn('con_struttura_sede_contatto', 'type', $this->integer(1));

        $this->addColumn('con_volontario_contatto', 'type', $this->integer(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_anagrafica_contatto', 'type');
        
        $this->dropColumn('con_mas_rubrica_contatto', 'type');
        
        $this->dropColumn('con_operatore_pc_contatto', 'type');
        
        $this->dropColumn('con_organizzazione_contatto', 'type');
        $this->dropColumn('con_sede_contatto', 'type');

        $this->dropColumn('con_ente_contatto', 'type');
        $this->dropColumn('con_ente_sede_contatto', 'type');

        $this->dropColumn('con_struttura_contatto', 'type');
        $this->dropColumn('con_struttura_sede_contatto', 'type');

        $this->dropColumn('con_volontario_contatto', 'type');
    }

   
}
