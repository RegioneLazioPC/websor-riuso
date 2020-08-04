<?php

use yii\db\Migration;

/**
 * Class m190510_064707_alter_con_contato_rows
 */
class m190510_064707_alter_con_contato_rows extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->addColumn('con_organizzazione_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_ente_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_struttura_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_organizzazione_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_ente_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_struttura_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_organizzazione_contatto', 'note', $this->string());
        $this->addColumn('con_ente_contatto', 'note', $this->string());
        $this->addColumn('con_struttura_contatto', 'note', $this->string());

        $this->addColumn('con_anagrafica_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_volontario_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_sede_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_struttura_sede_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_ente_sede_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        

        $this->addColumn('con_anagrafica_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_volontario_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_sede_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_struttura_sede_contatto', 'id_sync', $this->string(30));
        $this->addColumn('con_ente_sede_contatto', 'id_sync', $this->string(30));

        $this->addColumn('con_anagrafica_contatto', 'note', $this->string());
        $this->addColumn('con_volontario_contatto', 'note', $this->string());
        $this->addColumn('con_sede_contatto', 'note', $this->string());
        $this->addColumn('con_struttura_sede_contatto', 'note', $this->string());
        $this->addColumn('con_ente_sede_contatto', 'note', $this->string());

        $this->addColumn('con_mas_rubrica_contatto', 'use_type', $this->integer(1)->defaultValue(0));
        $this->addColumn('con_mas_rubrica_contatto', 'note', $this->string());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_organizzazione_contatto', 'use_type');
        $this->dropColumn('con_ente_contatto', 'use_type');
        $this->dropColumn('con_struttura_contatto', 'use_type');
        $this->dropColumn('con_organizzazione_contatto', 'id_sync');
        $this->dropColumn('con_ente_contatto', 'id_sync');
        $this->dropColumn('con_struttura_contatto', 'id_sync');
        $this->dropColumn('con_organizzazione_contatto', 'note');
        $this->dropColumn('con_ente_contatto', 'note');
        $this->dropColumn('con_struttura_contatto', 'note');

        $this->dropColumn('con_anagrafica_contatto', 'use_type');
        $this->dropColumn('con_volontario_contatto', 'use_type');
        $this->dropColumn('con_sede_contatto', 'use_type');
        $this->dropColumn('con_struttura_sede_contatto', 'use_type');
        $this->dropColumn('con_ente_sede_contatto', 'use_type');
        

        $this->dropColumn('con_anagrafica_contatto', 'id_sync');
        $this->dropColumn('con_volontario_contatto', 'id_sync');
        $this->dropColumn('con_sede_contatto', 'id_sync');
        $this->dropColumn('con_struttura_sede_contatto', 'id_sync');
        $this->dropColumn('con_ente_sede_contatto', 'id_sync');

        $this->dropColumn('con_anagrafica_contatto', 'note');
        $this->dropColumn('con_volontario_contatto', 'note');
        $this->dropColumn('con_sede_contatto', 'note');
        $this->dropColumn('con_struttura_sede_contatto', 'note');
        $this->dropColumn('con_ente_sede_contatto', 'note');

        $this->dropColumn('con_mas_rubrica_contatto', 'use_type');
        $this->dropColumn('con_mas_rubrica_contatto', 'note');
    }

}
