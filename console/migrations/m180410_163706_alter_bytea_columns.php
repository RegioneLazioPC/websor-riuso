<?php

use yii\db\Migration;

/**
 * Class m180410_163706_alter_bytea_columns
 */
class m180410_163706_alter_bytea_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('alm_allerta_meteo', 'avviso_meteo');
        $this->dropColumn('alm_allerta_meteo', 'avviso_idro');

        $this->dropColumn('alm_con_zona_criticita', 'precipitazioni');
        $this->dropColumn('alm_con_zona_criticita', 'nevicate');
        $this->dropColumn('alm_con_zona_criticita', 'venti');
        $this->dropColumn('alm_con_zona_criticita', 'mareggiate');

        $this->dropColumn('con_operatore_task', 'is_task');
        

        $this->dropColumn('utl_evento', 'pericolo');
        $this->dropColumn('utl_evento', 'feriti');
        $this->dropColumn('utl_evento', 'vittime');
        $this->dropColumn('utl_evento', 'interruzione_viabilita');
        $this->dropColumn('utl_evento', 'aiuto_segnalatore');
        $this->dropColumn('utl_evento', 'is_public');


        $this->dropColumn('utl_extra_segnalazione', 'show_numero');
        $this->dropColumn('utl_extra_segnalazione', 'show_note');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_nuclei_familiari');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_disabili');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_sistemazione_parenti_amici');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_sistemazione_strutture_ricettive');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_sistemazione_area_ricovero');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_persone_isolate');

        $this->dropColumn('utl_segnalazione', 'foto_locale');
        $this->dropColumn('utl_segnalazione', 'pericolo');
        $this->dropColumn('utl_segnalazione', 'feriti');
        $this->dropColumn('utl_segnalazione', 'vittime');
        $this->dropColumn('utl_segnalazione', 'interruzione_viabilita');
        $this->dropColumn('utl_segnalazione', 'aiuto_segnalatore');

        $this->dropColumn('vol_volontario', 'operativo');




        $this->addColumn('alm_allerta_meteo', 'avviso_meteo', $this->boolean());
        $this->addColumn('alm_allerta_meteo', 'avviso_idro', $this->boolean());

        $this->addColumn('alm_con_zona_criticita', 'precipitazioni', $this->boolean());
        $this->addColumn('alm_con_zona_criticita', 'nevicate', $this->boolean());
        $this->addColumn('alm_con_zona_criticita', 'venti', $this->boolean());
        $this->addColumn('alm_con_zona_criticita', 'mareggiate', $this->boolean());

        $this->addColumn('con_operatore_task', 'is_task', $this->boolean());
        

        $this->addColumn('utl_evento', 'pericolo', $this->boolean());
        $this->addColumn('utl_evento', 'feriti', $this->boolean());
        $this->addColumn('utl_evento', 'vittime', $this->boolean());
        $this->addColumn('utl_evento', 'interruzione_viabilita', $this->boolean());
        $this->addColumn('utl_evento', 'aiuto_segnalatore', $this->boolean());
        $this->addColumn('utl_evento', 'is_public', $this->boolean());


        $this->addColumn('utl_extra_segnalazione', 'show_numero', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_note', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_num_nuclei_familiari', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_num_disabili', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_num_sistemazione_parenti_amici', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_num_sistemazione_strutture_ricettive', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_num_sistemazione_area_ricovero', $this->boolean());
        $this->addColumn('utl_extra_segnalazione', 'show_num_persone_isolate', $this->boolean());

        $this->addColumn('utl_segnalazione', 'foto_locale', $this->boolean());
        $this->addColumn('utl_segnalazione', 'pericolo', $this->boolean());
        $this->addColumn('utl_segnalazione', 'feriti', $this->boolean());
        $this->addColumn('utl_segnalazione', 'vittime', $this->boolean());
        $this->addColumn('utl_segnalazione', 'interruzione_viabilita', $this->boolean());
        $this->addColumn('utl_segnalazione', 'aiuto_segnalatore', $this->boolean());

        $this->addColumn('vol_volontario', 'operativo', $this->boolean());
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('alm_allerta_meteo', 'avviso_meteo');
        $this->dropColumn('alm_allerta_meteo', 'avviso_idro');

        $this->dropColumn('alm_con_zona_criticita', 'precipitazioni');
        $this->dropColumn('alm_con_zona_criticita', 'nevicate');
        $this->dropColumn('alm_con_zona_criticita', 'venti');
        $this->dropColumn('alm_con_zona_criticita', 'mareggiate');

        $this->dropColumn('con_operatore_task', 'is_task');
        

        $this->dropColumn('utl_evento', 'pericolo');
        $this->dropColumn('utl_evento', 'feriti');
        $this->dropColumn('utl_evento', 'vittime');
        $this->dropColumn('utl_evento', 'interruzione_viabilita');
        $this->dropColumn('utl_evento', 'aiuto_segnalatore');
        $this->dropColumn('utl_evento', 'is_public');


        $this->dropColumn('utl_extra_segnalazione', 'show_numero');
        $this->dropColumn('utl_extra_segnalazione', 'show_note');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_nuclei_familiari');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_disabili');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_sistemazione_parenti_amici');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_sistemazione_strutture_ricettive');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_sistemazione_area_ricovero');
        $this->dropColumn('utl_extra_segnalazione', 'show_num_persone_isolate');

        $this->dropColumn('utl_segnalazione', 'foto_locale');
        $this->dropColumn('utl_segnalazione', 'pericolo');
        $this->dropColumn('utl_segnalazione', 'feriti');
        $this->dropColumn('utl_segnalazione', 'vittime');
        $this->dropColumn('utl_segnalazione', 'interruzione_viabilita');
        $this->dropColumn('utl_segnalazione', 'aiuto_segnalatore');

        $this->dropColumn('vol_volontario', 'operativo');




        $this->addColumn('alm_allerta_meteo', 'avviso_meteo', $this->binary());
        $this->addColumn('alm_allerta_meteo', 'avviso_idro', $this->binary());

        $this->addColumn('alm_con_zona_criticita', 'precipitazioni', $this->binary());
        $this->addColumn('alm_con_zona_criticita', 'nevicate', $this->binary());
        $this->addColumn('alm_con_zona_criticita', 'venti', $this->binary());
        $this->addColumn('alm_con_zona_criticita', 'mareggiate', $this->binary());

        $this->addColumn('con_operatore_task', 'is_task', $this->binary());
        

        $this->addColumn('utl_evento', 'pericolo', $this->binary());
        $this->addColumn('utl_evento', 'feriti', $this->binary());
        $this->addColumn('utl_evento', 'vittime', $this->binary());
        $this->addColumn('utl_evento', 'interruzione_viabilita', $this->binary());
        $this->addColumn('utl_evento', 'aiuto_segnalatore', $this->binary());
        $this->addColumn('utl_evento', 'is_public', $this->binary());


        $this->addColumn('utl_extra_segnalazione', 'show_numero', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_note', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_num_nuclei_familiari', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_num_disabili', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_num_sistemazione_parenti_amici', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_num_sistemazione_strutture_ricettive', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_num_sistemazione_area_ricovero', $this->binary());
        $this->addColumn('utl_extra_segnalazione', 'show_num_persone_isolate', $this->binary());

        $this->addColumn('utl_segnalazione', 'foto_locale', $this->binary());
        $this->addColumn('utl_segnalazione', 'pericolo', $this->binary());
        $this->addColumn('utl_segnalazione', 'feriti', $this->binary());
        $this->addColumn('utl_segnalazione', 'vittime', $this->binary());
        $this->addColumn('utl_segnalazione', 'interruzione_viabilita', $this->binary());
        $this->addColumn('utl_segnalazione', 'aiuto_segnalatore', $this->binary());

        $this->addColumn('vol_volontario', 'operativo', $this->binary());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180410_163706_alter_bytea_columns cannot be reverted.\n";

        return false;
    }
    */
}
