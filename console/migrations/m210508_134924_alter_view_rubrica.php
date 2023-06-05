<?php

use yii\db\Migration;

/**
 * Class m210508_134924_alter_view_rubrica
 */
class m210508_134924_alter_view_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_rubrica")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_rubrica AS 
            SELECT t.id_contatto,
    t.valore_contatto,
    t.use_type,
    t.check_mobile,
    t.check_predefinito,
    t.contatto_type,
    t.valore_riferimento,
    t.tipo_contatto,
    t.note,
    t.tipologia_riferimento,
    t.lat,
    t.lon,
    t.geom,
    t.id_riferimento,
    t.tipo_riferimento,
    t.id_anagrafica,
    t.indirizzo,
    t.comune,
    t.provincia,
    t.entita,
    t.vendor,
    t.zone_allerta,
    t.num_elenco_territoriale,
    concat(t.entita, '_', t.id_riferimento) AS identificativo,
    concat(con_view_rubrica_everbridge_ext_ids.ext_id) AS ext_id,
    concat(con_view_rubrica_everbridge_ext_ids.ext_id, '_', con_view_rubrica_everbridge_ext_ids.delivery_path) AS everbridge_identifier,
    json_agg(DISTINCT rubrica_group.id) FILTER (WHERE (rubrica_group.id IS NOT NULL)) AS gruppi,
    t.cf
   FROM (((( SELECT DISTINCT ON (con_struttura_contatto.id) con_struttura_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_struttura_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_struttura_contatto'::text AS contatto_type,
            str_struttura.denominazione AS valore_riferimento,
            con_struttura_contatto.type AS tipo_contatto,
            con_struttura_contatto.note,
                CASE
                    WHEN ((str_tipo_struttura.descrizione)::text ~~* 'comunita'' montana'::text) THEN 'comunita'' montana'::text
                    ELSE 'struttura'::text
                END AS tipologia_riferimento,
            (str_struttura_sede.lat)::text AS lat,
            (str_struttura_sede.lon)::text AS lon,
            ''::text AS geom,
            str_struttura.id AS id_riferimento,
            'id_struttura'::text AS tipo_riferimento,
            '-1'::integer AS id_anagrafica,
            str_struttura_sede.indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'struttura'::text AS entita,
            utl_contatto.vendor,
            str_struttura.zone_allerta,
            NULL::integer AS num_elenco_territoriale,
            str_struttura.codicefiscale as cf
           FROM ((((((con_struttura_contatto
             LEFT JOIN str_struttura ON ((con_struttura_contatto.id_struttura = str_struttura.id)))
             LEFT JOIN utl_contatto ON ((utl_contatto.id = con_struttura_contatto.id_contatto)))
             LEFT JOIN str_struttura_sede ON ((str_struttura_sede.id_struttura = str_struttura.id)))
             LEFT JOIN str_tipo_struttura ON ((str_tipo_struttura.id = str_struttura.id_tipo_struttura)))
             LEFT JOIN loc_comune ON ((loc_comune.id = str_struttura_sede.id_comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION ALL
         SELECT DISTINCT ON (con_ente_contatto.id) con_ente_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_ente_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_ente_contatto'::text AS contatto_type,
            ent_ente.denominazione AS valore_riferimento,
            con_ente_contatto.type AS tipo_contatto,
            con_ente_contatto.note,
                CASE
                    WHEN ((ent_tipo_ente.descrizione)::text ~~* 'comune'::text) THEN 'comune'::text
                    WHEN ((ent_tipo_ente.descrizione)::text ~~* 'prefettura'::text) THEN 'prefettura'::text
                    ELSE 'ente'::text
                END AS tipologia_riferimento,
            (ent_ente_sede.lat)::text AS lat,
            (ent_ente_sede.lon)::text AS lon,
            ''::text AS geom,
            ent_ente.id AS id_riferimento,
            'id_ente'::text AS tipo_riferimento,
            '-1'::integer AS id_anagrafica,
            ent_ente_sede.indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'ente'::text AS entita,
            utl_contatto.vendor,
            ent_ente.zone_allerta,
            NULL::integer AS num_elenco_territoriale,
            ent_ente.codicefiscale as cf
           FROM ((((((con_ente_contatto
             LEFT JOIN ent_ente ON ((con_ente_contatto.id_ente = ent_ente.id)))
             LEFT JOIN utl_contatto ON ((utl_contatto.id = con_ente_contatto.id_contatto)))
             LEFT JOIN ent_ente_sede ON ((ent_ente_sede.id_ente = ent_ente.id)))
             LEFT JOIN ent_tipo_ente ON ((ent_tipo_ente.id = ent_ente.id_tipo_ente)))
             LEFT JOIN loc_comune ON ((loc_comune.id = ent_ente_sede.id_comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION ALL
         SELECT DISTINCT ON (con_organizzazione_contatto.id) con_organizzazione_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_organizzazione_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_organizzazione_contatto'::text AS contatto_type,
            concat(vol_organizzazione.ref_id, '_', vol_organizzazione.denominazione) AS valore_riferimento,
            con_organizzazione_contatto.type AS tipo_contatto,
            con_organizzazione_contatto.note,
            'organizzazione'::text AS tipologia_riferimento,
            (vol_sede.lat)::text AS lat,
            (vol_sede.lon)::text AS lon,
            (vol_sede.geom)::text AS geom,
            vol_organizzazione.id AS id_riferimento,
            'id_organizzazione'::text AS tipo_riferimento,
            '-1'::integer AS id_anagrafica,
            concat(vol_sede.indirizzo, ' ', vol_sede.cap) AS indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'organizzazione'::text AS entita,
            utl_contatto.vendor,
            vol_organizzazione.zone_allerta,
            vol_organizzazione.ref_id AS num_elenco_territoriale,
            vol_organizzazione.codicefiscale as cf
           FROM (((((con_organizzazione_contatto
             LEFT JOIN vol_organizzazione ON ((con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id)))
             LEFT JOIN utl_contatto ON ((utl_contatto.id = con_organizzazione_contatto.id_contatto)))
             LEFT JOIN vol_sede ON (((vol_sede.id_organizzazione = vol_organizzazione.id) AND (vol_sede.tipo = 'Sede Legale'::vol_sede_tipo))))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_contatto.contatto IS NOT NULL) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION ALL
         SELECT DISTINCT ON (con_mas_rubrica_contatto.id) con_mas_rubrica_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_mas_rubrica_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_mas_rubrica_contatto'::text AS contatto_type,
            concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
            con_mas_rubrica_contatto.type AS tipo_contatto,
            con_mas_rubrica_contatto.note,
            'mas_rubrica'::text AS tipologia_riferimento,
            (mas_rubrica.lat)::text AS lat,
            (mas_rubrica.lon)::text AS lon,
            (mas_rubrica.geom)::text AS geom,
            mas_rubrica.id AS id_riferimento,
            'id_mas_rubrica'::text AS tipo_riferimento,
            utl_anagrafica.id AS id_anagrafica,
            concat(utl_indirizzo.indirizzo, ' ', utl_indirizzo.civico, ' ', utl_indirizzo.cap) AS indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'mas_rubrica'::text AS entita,
            utl_contatto.vendor,
            ''::character varying AS zone_allerta,
            NULL::integer AS num_elenco_territoriale,
            utl_anagrafica.codfiscale as cf
           FROM ((((((con_mas_rubrica_contatto
             LEFT JOIN mas_rubrica ON ((con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id)))
             LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = mas_rubrica.id_anagrafica)))
             LEFT JOIN utl_contatto ON ((con_mas_rubrica_contatto.id_contatto = utl_contatto.id)))
             LEFT JOIN utl_indirizzo ON ((utl_indirizzo.id = mas_rubrica.id_indirizzo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = utl_indirizzo.id_comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (utl_contatto.id IS NOT NULL)
        UNION ALL
         SELECT DISTINCT ON (con_operatore_pc_contatto.id) con_operatore_pc_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_operatore_pc_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_operatore_pc_contatto'::text AS contatto_type,
            concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
            con_operatore_pc_contatto.type AS tipo_contatto,
            ''::text AS note,
            'operatore pc'::text AS tipologia_riferimento,
            ''::text AS lat,
            ''::text AS lon,
            ''::text AS geom,
            utl_operatore_pc.id AS id_riferimento,
            'id_operatore_pc'::text AS tipo_riferimento,
            utl_anagrafica.id AS id_anagrafica,
            ''::text AS indirizzo,
            ''::text AS comune,
            ''::text AS provincia,
            'operatore_pc'::text AS entita,
            utl_contatto.vendor,
            ''::character varying AS zone_allerta,
            NULL::integer AS num_elenco_territoriale,
            utl_anagrafica.codfiscale as cf
           FROM (((con_operatore_pc_contatto
             LEFT JOIN utl_operatore_pc ON ((con_operatore_pc_contatto.id_operatore_pc = utl_operatore_pc.id)))
             LEFT JOIN utl_contatto ON ((con_operatore_pc_contatto.id_contatto = utl_contatto.id)))
             LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = utl_operatore_pc.id_anagrafica)))
          WHERE (utl_contatto.id IS NOT NULL)) t
     LEFT JOIN con_view_rubrica_everbridge_ext_ids ON (((con_view_rubrica_everbridge_ext_ids.contatto)::text = concat(t.id_contatto, '_', t.contatto_type))))
     LEFT JOIN con_rubrica_group_contact ON (((con_rubrica_group_contact.id_rubrica_contatto = t.id_riferimento) AND ((con_rubrica_group_contact.tipo_rubrica_contatto)::text = t.tipo_riferimento))))
     LEFT JOIN rubrica_group ON ((rubrica_group.id = con_rubrica_group_contact.id_group)))
  GROUP BY t.id_contatto, t.valore_contatto, t.use_type, t.check_mobile, t.check_predefinito, t.contatto_type, t.valore_riferimento, t.tipo_contatto, t.note, t.tipologia_riferimento, t.lat, t.lon, t.geom, t.id_riferimento, t.tipo_riferimento, t.id_anagrafica, t.indirizzo, t.comune, t.provincia, t.entita, t.vendor, t.zone_allerta, t.num_elenco_territoriale, t.cf, con_view_rubrica_everbridge_ext_ids.ext_id, con_view_rubrica_everbridge_ext_ids.delivery_path;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_rubrica")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_rubrica AS 
            SELECT t.id_contatto,
    t.valore_contatto,
    t.use_type,
    t.check_mobile,
    t.check_predefinito,
    t.contatto_type,
    t.valore_riferimento,
    t.tipo_contatto,
    t.note,
    t.tipologia_riferimento,
    t.lat,
    t.lon,
    t.geom,
    t.id_riferimento,
    t.tipo_riferimento,
    t.id_anagrafica,
    t.indirizzo,
    t.comune,
    t.provincia,
    t.entita,
    t.vendor,
    t.zone_allerta,
    t.num_elenco_territoriale,
    concat(t.entita, '_', t.id_riferimento) AS identificativo,
    concat(con_view_rubrica_everbridge_ext_ids.ext_id) AS ext_id,
    concat(con_view_rubrica_everbridge_ext_ids.ext_id, '_', con_view_rubrica_everbridge_ext_ids.delivery_path) AS everbridge_identifier,
    json_agg(DISTINCT rubrica_group.id) FILTER (WHERE (rubrica_group.id IS NOT NULL)) AS gruppi
   FROM (((( SELECT DISTINCT ON (con_struttura_contatto.id) con_struttura_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_struttura_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_struttura_contatto'::text AS contatto_type,
            str_struttura.denominazione AS valore_riferimento,
            con_struttura_contatto.type AS tipo_contatto,
            con_struttura_contatto.note,
                CASE
                    WHEN ((str_tipo_struttura.descrizione)::text ~~* 'comunita'' montana'::text) THEN 'comunita'' montana'::text
                    ELSE 'struttura'::text
                END AS tipologia_riferimento,
            (str_struttura_sede.lat)::text AS lat,
            (str_struttura_sede.lon)::text AS lon,
            ''::text AS geom,
            str_struttura.id AS id_riferimento,
            'id_struttura'::text AS tipo_riferimento,
            '-1'::integer AS id_anagrafica,
            str_struttura_sede.indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'struttura'::text AS entita,
            utl_contatto.vendor,
            str_struttura.zone_allerta,
            NULL::integer AS num_elenco_territoriale
           FROM ((((((con_struttura_contatto
             LEFT JOIN str_struttura ON ((con_struttura_contatto.id_struttura = str_struttura.id)))
             LEFT JOIN utl_contatto ON ((utl_contatto.id = con_struttura_contatto.id_contatto)))
             LEFT JOIN str_struttura_sede ON ((str_struttura_sede.id_struttura = str_struttura.id)))
             LEFT JOIN str_tipo_struttura ON ((str_tipo_struttura.id = str_struttura.id_tipo_struttura)))
             LEFT JOIN loc_comune ON ((loc_comune.id = str_struttura_sede.id_comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION ALL
         SELECT DISTINCT ON (con_ente_contatto.id) con_ente_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_ente_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_ente_contatto'::text AS contatto_type,
            ent_ente.denominazione AS valore_riferimento,
            con_ente_contatto.type AS tipo_contatto,
            con_ente_contatto.note,
                CASE
                    WHEN ((ent_tipo_ente.descrizione)::text ~~* 'comune'::text) THEN 'comune'::text
                    WHEN ((ent_tipo_ente.descrizione)::text ~~* 'prefettura'::text) THEN 'prefettura'::text
                    ELSE 'ente'::text
                END AS tipologia_riferimento,
            (ent_ente_sede.lat)::text AS lat,
            (ent_ente_sede.lon)::text AS lon,
            ''::text AS geom,
            ent_ente.id AS id_riferimento,
            'id_ente'::text AS tipo_riferimento,
            '-1'::integer AS id_anagrafica,
            ent_ente_sede.indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'ente'::text AS entita,
            utl_contatto.vendor,
            ent_ente.zone_allerta,
            NULL::integer AS num_elenco_territoriale
           FROM ((((((con_ente_contatto
             LEFT JOIN ent_ente ON ((con_ente_contatto.id_ente = ent_ente.id)))
             LEFT JOIN utl_contatto ON ((utl_contatto.id = con_ente_contatto.id_contatto)))
             LEFT JOIN ent_ente_sede ON ((ent_ente_sede.id_ente = ent_ente.id)))
             LEFT JOIN ent_tipo_ente ON ((ent_tipo_ente.id = ent_ente.id_tipo_ente)))
             LEFT JOIN loc_comune ON ((loc_comune.id = ent_ente_sede.id_comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION ALL
         SELECT DISTINCT ON (con_organizzazione_contatto.id) con_organizzazione_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_organizzazione_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_organizzazione_contatto'::text AS contatto_type,
            concat(vol_organizzazione.ref_id, '_', vol_organizzazione.denominazione) AS valore_riferimento,
            con_organizzazione_contatto.type AS tipo_contatto,
            con_organizzazione_contatto.note,
            'organizzazione'::text AS tipologia_riferimento,
            (vol_sede.lat)::text AS lat,
            (vol_sede.lon)::text AS lon,
            (vol_sede.geom)::text AS geom,
            vol_organizzazione.id AS id_riferimento,
            'id_organizzazione'::text AS tipo_riferimento,
            '-1'::integer AS id_anagrafica,
            concat(vol_sede.indirizzo, ' ', vol_sede.cap) AS indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'organizzazione'::text AS entita,
            utl_contatto.vendor,
            vol_organizzazione.zone_allerta,
            vol_organizzazione.ref_id AS num_elenco_territoriale
           FROM (((((con_organizzazione_contatto
             LEFT JOIN vol_organizzazione ON ((con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id)))
             LEFT JOIN utl_contatto ON ((utl_contatto.id = con_organizzazione_contatto.id_contatto)))
             LEFT JOIN vol_sede ON (((vol_sede.id_organizzazione = vol_organizzazione.id) AND (vol_sede.tipo = 'Sede Legale'::vol_sede_tipo))))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_contatto.contatto IS NOT NULL) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION ALL
         SELECT DISTINCT ON (con_mas_rubrica_contatto.id) con_mas_rubrica_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_mas_rubrica_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_mas_rubrica_contatto'::text AS contatto_type,
            concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
            con_mas_rubrica_contatto.type AS tipo_contatto,
            con_mas_rubrica_contatto.note,
            'mas_rubrica'::text AS tipologia_riferimento,
            (mas_rubrica.lat)::text AS lat,
            (mas_rubrica.lon)::text AS lon,
            (mas_rubrica.geom)::text AS geom,
            mas_rubrica.id AS id_riferimento,
            'id_mas_rubrica'::text AS tipo_riferimento,
            utl_anagrafica.id AS id_anagrafica,
            concat(utl_indirizzo.indirizzo, ' ', utl_indirizzo.civico, ' ', utl_indirizzo.cap) AS indirizzo,
            loc_comune.comune,
            loc_provincia.sigla AS provincia,
            'mas_rubrica'::text AS entita,
            utl_contatto.vendor,
            ''::character varying AS zone_allerta,
            NULL::integer AS num_elenco_territoriale
           FROM ((((((con_mas_rubrica_contatto
             LEFT JOIN mas_rubrica ON ((con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id)))
             LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = mas_rubrica.id_anagrafica)))
             LEFT JOIN utl_contatto ON ((con_mas_rubrica_contatto.id_contatto = utl_contatto.id)))
             LEFT JOIN utl_indirizzo ON ((utl_indirizzo.id = mas_rubrica.id_indirizzo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = utl_indirizzo.id_comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (utl_contatto.id IS NOT NULL)
        UNION ALL
         SELECT DISTINCT ON (con_operatore_pc_contatto.id) con_operatore_pc_contatto.id_contatto,
            utl_contatto.contatto AS valore_contatto,
            con_operatore_pc_contatto.use_type,
            utl_contatto.check_mobile,
            utl_contatto.check_predefinito,
            'con_operatore_pc_contatto'::text AS contatto_type,
            concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
            con_operatore_pc_contatto.type AS tipo_contatto,
            ''::text AS note,
            'operatore pc'::text AS tipologia_riferimento,
            ''::text AS lat,
            ''::text AS lon,
            ''::text AS geom,
            utl_operatore_pc.id AS id_riferimento,
            'id_operatore_pc'::text AS tipo_riferimento,
            utl_anagrafica.id AS id_anagrafica,
            ''::text AS indirizzo,
            ''::text AS comune,
            ''::text AS provincia,
            'operatore_pc'::text AS entita,
            utl_contatto.vendor,
            ''::character varying AS zone_allerta,
            NULL::integer AS num_elenco_territoriale
           FROM (((con_operatore_pc_contatto
             LEFT JOIN utl_operatore_pc ON ((con_operatore_pc_contatto.id_operatore_pc = utl_operatore_pc.id)))
             LEFT JOIN utl_contatto ON ((con_operatore_pc_contatto.id_contatto = utl_contatto.id)))
             LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = utl_operatore_pc.id_anagrafica)))
          WHERE (utl_contatto.id IS NOT NULL)) t
     LEFT JOIN con_view_rubrica_everbridge_ext_ids ON (((con_view_rubrica_everbridge_ext_ids.contatto)::text = concat(t.id_contatto, '_', t.contatto_type))))
     LEFT JOIN con_rubrica_group_contact ON (((con_rubrica_group_contact.id_rubrica_contatto = t.id_riferimento) AND ((con_rubrica_group_contact.tipo_rubrica_contatto)::text = t.tipo_riferimento))))
     LEFT JOIN rubrica_group ON ((rubrica_group.id = con_rubrica_group_contact.id_group)))
  GROUP BY t.id_contatto, t.valore_contatto, t.use_type, t.check_mobile, t.check_predefinito, t.contatto_type, t.valore_riferimento, t.tipo_contatto, t.note, t.tipologia_riferimento, t.lat, t.lon, t.geom, t.id_riferimento, t.tipo_riferimento, t.id_anagrafica, t.indirizzo, t.comune, t.provincia, t.entita, t.vendor, t.zone_allerta, t.num_elenco_territoriale, con_view_rubrica_everbridge_ext_ids.ext_id, con_view_rubrica_everbridge_ext_ids.delivery_path;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210508_134924_alter_view_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
