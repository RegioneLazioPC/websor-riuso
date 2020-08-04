<?php

use yii\db\Migration;

/**
 * Class m190510_084545_view_for_rubrica
 */
class m190510_084545_view_for_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_strutture")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_enti")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_contatti_rubrica")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_operatore_pc")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica_strutture as
            SELECT utl_contatto.contatto AS valore_contatto,
                con_struttura_contatto.use_type,
                utl_contatto.id AS id_contatto,
                utl_contatto.check_mobile,
                utl_contatto.check_predefinito,
                'utl_contatto'::text AS contatto_type,
                str_struttura.denominazione AS valore_riferimento,
                utl_contatto.type AS tipo_contatto,
                'struttura'::text AS tipologia_riferimento,
                str_struttura_sede.lat::TEXT,
                str_struttura_sede.lon::TEXT,
                null as geom,
                str_struttura.id AS id_riferimento,
                'id_struttura'::text AS tipo_riferimento,
                '-1'::integer AS id_anagrafica,
                str_struttura_sede.indirizzo AS indirizzo,
                loc_comune.comune,
                loc_provincia.sigla AS provincia,
                utl_contatto.vendor
               FROM con_struttura_contatto
                 LEFT JOIN str_struttura ON con_struttura_contatto.id_struttura = str_struttura.id
                 LEFT JOIN utl_contatto ON utl_contatto.id = con_struttura_contatto.id_contatto
                 LEFT JOIN str_struttura_sede ON str_struttura_sede.id_struttura = str_struttura.id
                 LEFT JOIN loc_comune ON loc_comune.id = str_struttura_sede.id_comune
                 LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia"
        )->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica_enti as
            SELECT utl_contatto.contatto AS valore_contatto,
                con_ente_contatto.use_type,
                utl_contatto.id AS id_contatto,
                utl_contatto.check_mobile,
                utl_contatto.check_predefinito,
                'utl_contatto'::text AS contatto_type,
                ent_ente.denominazione AS valore_riferimento,
                utl_contatto.type AS tipo_contatto,
                'ente'::text AS tipologia_riferimento,
                ent_ente_sede.lat::TEXT,
                ent_ente_sede.lon::TEXT,
                null as geom,
                ent_ente.id AS id_riferimento,
                'id_ente'::text AS tipo_riferimento,
                '-1'::integer AS id_anagrafica,
                ent_ente_sede.indirizzo AS indirizzo,
                loc_comune.comune,
                loc_provincia.sigla AS provincia,
                utl_contatto.vendor                
               FROM con_ente_contatto
                 LEFT JOIN ent_ente ON con_ente_contatto.id_ente = ent_ente.id
                 LEFT JOIN utl_contatto ON utl_contatto.id = con_ente_contatto.id_contatto
                 LEFT JOIN ent_ente_sede ON ent_ente_sede.id_ente = ent_ente.id
                 LEFT JOIN loc_comune ON loc_comune.id = ent_ente_sede.id_comune
                 LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
              WHERE (utl_contatto.contatto IS NOT NULL)"
        )->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica_organizzazioni as
            SELECT utl_contatto.contatto AS valore_contatto,
                con_organizzazione_contatto.use_type,
                utl_contatto.id AS id_contatto,
                utl_contatto.check_mobile,
                utl_contatto.check_predefinito,
                'utl_contatto'::text AS contatto_type,
                vol_organizzazione.denominazione AS valore_riferimento,
                utl_contatto.type AS tipo_contatto,
                'organizzazione'::text AS tipologia_riferimento,
                vol_sede.lat::TEXT,
                vol_sede.lon::TEXT,
                vol_sede.geom::TEXT,
                vol_organizzazione.id AS id_riferimento,
                'id_organizzazione'::text AS tipo_riferimento,
                '-1'::integer AS id_anagrafica,
                concat(vol_sede.indirizzo, ' ', vol_sede.cap) AS indirizzo,
                loc_comune.comune,
                loc_provincia.sigla AS provincia,
                utl_contatto.vendor
               FROM con_organizzazione_contatto
                 LEFT JOIN vol_organizzazione ON con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id
                 LEFT JOIN utl_contatto ON utl_contatto.id = con_organizzazione_contatto.id_contatto
                 LEFT JOIN vol_sede ON vol_sede.id_organizzazione = vol_organizzazione.id AND vol_sede.tipo = 'Sede Legale'::vol_sede_tipo
                 LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                 LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
              WHERE (utl_contatto.contatto IS NOT NULL)"
        )->execute();



        Yii::$app->db->createCommand("CREATE VIEW view_rubrica_contatti_rubrica as
            SELECT 
                utl_contatto.contatto as valore_contatto,
                con_mas_rubrica_contatto.use_type, 
                utl_contatto.id as id_contatto,
                utl_contatto.check_mobile as check_mobile,
                utl_contatto.check_predefinito as check_predefinito,
                'utl_contatto' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                utl_contatto.type as tipo_contatto, 
                mas_rubrica.ruolo as tipologia_riferimento,
                mas_rubrica.lat::TEXT as lat,
                mas_rubrica.lon::TEXT as lon,
                mas_rubrica.geom::TEXT as geom,
                mas_rubrica.id as id_riferimento,
                'id_mas_rubrica' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(utl_indirizzo.indirizzo,' ',utl_indirizzo.civico,' ',utl_indirizzo.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia,
                utl_contatto.vendor as vendor
                FROM con_mas_rubrica_contatto
            LEFT JOIN mas_rubrica ON con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = mas_rubrica.id_anagrafica
            LEFT JOIN utl_contatto ON con_mas_rubrica_contatto.id_contatto = utl_contatto.id
            LEFT JOIN utl_indirizzo ON utl_indirizzo.id = mas_rubrica.id_indirizzo
            LEFT JOIN loc_comune on loc_comune.id = utl_indirizzo.id_comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_contatto.id is not null"
        )->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica_operatore_pc as
            SELECT 
                utl_contatto.contatto as valore_contatto,
                con_operatore_pc_contatto.use_type, 
                utl_contatto.id as id_contatto,
                utl_contatto.check_mobile as check_mobile,
                utl_contatto.check_predefinito as check_predefinito,
                'utl_contatto' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                utl_contatto.type as tipo_contatto, 
                'operatore pc' as tipologia_riferimento,
                null as lat,
                null as lon,
                null as geom,
                utl_operatore_pc.id as id_riferimento,
                'id_operatore_pc' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                null as indirizzo,
                null as comune,
                null as provincia,
                utl_contatto.vendor as vendor
                FROM con_operatore_pc_contatto
            LEFT JOIN utl_operatore_pc ON con_operatore_pc_contatto.id_operatore_pc = utl_operatore_pc.id
            LEFT JOIN utl_contatto ON con_operatore_pc_contatto.id_contatto = utl_contatto.id
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_operatore_pc.id_anagrafica
            WHERE utl_contatto.id is not null"
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_operatore_pc")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_contatti_rubrica")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_strutture")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_enti")->execute();
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica_organizzazioni")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_084545_view_for_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
