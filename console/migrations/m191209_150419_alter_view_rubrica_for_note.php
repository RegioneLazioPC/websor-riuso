<?php

use yii\db\Migration;

/**
 * Class m191209_150419_alter_view_rubrica_for_note
 */
class m191209_150419_alter_view_rubrica_for_note extends Migration
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
            concat(t.entita, '_', t.id_riferimento) AS identificativo,
            concat(con_view_rubrica_everbridge_ext_ids.ext_id) AS ext_id,
            concat(con_view_rubrica_everbridge_ext_ids.ext_id, '_', con_view_rubrica_everbridge_ext_ids.delivery_path) AS everbridge_identifier
           FROM (( SELECT DISTINCT ON (con_struttura_contatto.id) con_struttura_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_struttura_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_struttura_contatto'::text AS contatto_type,
                    str_struttura.denominazione AS valore_riferimento,
                    con_struttura_contatto.type AS tipo_contatto,
                    con_struttura_contatto.note AS note,
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
                    utl_contatto.vendor
                   FROM ((((((con_struttura_contatto
                     LEFT JOIN str_struttura ON ((con_struttura_contatto.id_struttura = str_struttura.id)))
                     LEFT JOIN utl_contatto ON ((utl_contatto.id = con_struttura_contatto.id_contatto)))
                     LEFT JOIN str_struttura_sede ON ((str_struttura_sede.id_struttura = str_struttura.id)))
                     LEFT JOIN str_tipo_struttura ON ((str_tipo_struttura.id = str_struttura.id_tipo_struttura)))
                     LEFT JOIN loc_comune ON ((loc_comune.id = str_struttura_sede.id_comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                UNION
                 SELECT DISTINCT ON (con_ente_contatto.id) con_ente_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_ente_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_ente_contatto'::text AS contatto_type,
                    ent_ente.denominazione AS valore_riferimento,
                    con_ente_contatto.type AS tipo_contatto,
                    con_ente_contatto.note AS note,
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
                    utl_contatto.vendor
                   FROM ((((((con_ente_contatto
                     LEFT JOIN ent_ente ON ((con_ente_contatto.id_ente = ent_ente.id)))
                     LEFT JOIN utl_contatto ON ((utl_contatto.id = con_ente_contatto.id_contatto)))
                     LEFT JOIN ent_ente_sede ON ((ent_ente_sede.id_ente = ent_ente.id)))
                     LEFT JOIN ent_tipo_ente ON ((ent_tipo_ente.id = ent_ente.id_tipo_ente)))
                     LEFT JOIN loc_comune ON ((loc_comune.id = ent_ente_sede.id_comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                UNION
                 SELECT DISTINCT ON (con_organizzazione_contatto.id) con_organizzazione_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_organizzazione_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_organizzazione_contatto'::text AS contatto_type,
                    concat(vol_organizzazione.ref_id, '_', vol_organizzazione.denominazione) AS valore_riferimento,
                    con_organizzazione_contatto.type AS tipo_contatto,
                    con_organizzazione_contatto.note AS note,
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
                    utl_contatto.vendor
                   FROM (((((con_organizzazione_contatto
                     LEFT JOIN vol_organizzazione ON ((con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id)))
                     LEFT JOIN utl_contatto ON ((utl_contatto.id = con_organizzazione_contatto.id_contatto)))
                     LEFT JOIN vol_sede ON (((vol_sede.id_organizzazione = vol_organizzazione.id) AND (vol_sede.tipo = 'Sede Legale'::vol_sede_tipo))))
                     LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                  WHERE ((utl_contatto.contatto IS NOT NULL) AND (vol_organizzazione.stato_iscrizione = 3))
                UNION
                 SELECT DISTINCT ON (con_mas_rubrica_contatto.id) con_mas_rubrica_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_mas_rubrica_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_mas_rubrica_contatto'::text AS contatto_type,
                    concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
                    con_mas_rubrica_contatto.type AS tipo_contatto,
                    con_mas_rubrica_contatto.note AS note,
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
                    utl_contatto.vendor
                   FROM ((((((con_mas_rubrica_contatto
                     LEFT JOIN mas_rubrica ON ((con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id)))
                     LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = mas_rubrica.id_anagrafica)))
                     LEFT JOIN utl_contatto ON ((con_mas_rubrica_contatto.id_contatto = utl_contatto.id)))
                     LEFT JOIN utl_indirizzo ON ((utl_indirizzo.id = mas_rubrica.id_indirizzo)))
                     LEFT JOIN loc_comune ON ((loc_comune.id = utl_indirizzo.id_comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                  WHERE (utl_contatto.id IS NOT NULL)
                UNION
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
                    utl_contatto.vendor
                   FROM (((con_operatore_pc_contatto
                     LEFT JOIN utl_operatore_pc ON ((con_operatore_pc_contatto.id_operatore_pc = utl_operatore_pc.id)))
                     LEFT JOIN utl_contatto ON ((con_operatore_pc_contatto.id_contatto = utl_contatto.id)))
                     LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = utl_operatore_pc.id_anagrafica)))
                  WHERE (utl_contatto.id IS NOT NULL)) t
             LEFT JOIN con_view_rubrica_everbridge_ext_ids ON (((con_view_rubrica_everbridge_ext_ids.contatto)::text = concat(t.id_contatto, '_', t.contatto_type))));")->execute();
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
            concat(t.entita, '_', t.id_riferimento) AS identificativo,
            concat(con_view_rubrica_everbridge_ext_ids.ext_id) AS ext_id,
            concat(con_view_rubrica_everbridge_ext_ids.ext_id, '_', con_view_rubrica_everbridge_ext_ids.delivery_path) AS everbridge_identifier
           FROM (( SELECT DISTINCT ON (con_struttura_contatto.id) con_struttura_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_struttura_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_struttura_contatto'::text AS contatto_type,
                    str_struttura.denominazione AS valore_riferimento,
                    con_struttura_contatto.type AS tipo_contatto,
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
                    utl_contatto.vendor
                   FROM ((((((con_struttura_contatto
                     LEFT JOIN str_struttura ON ((con_struttura_contatto.id_struttura = str_struttura.id)))
                     LEFT JOIN utl_contatto ON ((utl_contatto.id = con_struttura_contatto.id_contatto)))
                     LEFT JOIN str_struttura_sede ON ((str_struttura_sede.id_struttura = str_struttura.id)))
                     LEFT JOIN str_tipo_struttura ON ((str_tipo_struttura.id = str_struttura.id_tipo_struttura)))
                     LEFT JOIN loc_comune ON ((loc_comune.id = str_struttura_sede.id_comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                UNION
                 SELECT DISTINCT ON (con_ente_contatto.id) con_ente_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_ente_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_ente_contatto'::text AS contatto_type,
                    ent_ente.denominazione AS valore_riferimento,
                    con_ente_contatto.type AS tipo_contatto,
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
                    utl_contatto.vendor
                   FROM ((((((con_ente_contatto
                     LEFT JOIN ent_ente ON ((con_ente_contatto.id_ente = ent_ente.id)))
                     LEFT JOIN utl_contatto ON ((utl_contatto.id = con_ente_contatto.id_contatto)))
                     LEFT JOIN ent_ente_sede ON ((ent_ente_sede.id_ente = ent_ente.id)))
                     LEFT JOIN ent_tipo_ente ON ((ent_tipo_ente.id = ent_ente.id_tipo_ente)))
                     LEFT JOIN loc_comune ON ((loc_comune.id = ent_ente_sede.id_comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                UNION
                 SELECT DISTINCT ON (con_organizzazione_contatto.id) con_organizzazione_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_organizzazione_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_organizzazione_contatto'::text AS contatto_type,
                    concat(vol_organizzazione.ref_id, '_', vol_organizzazione.denominazione) AS valore_riferimento,
                    con_organizzazione_contatto.type AS tipo_contatto,
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
                    utl_contatto.vendor
                   FROM (((((con_organizzazione_contatto
                     LEFT JOIN vol_organizzazione ON ((con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id)))
                     LEFT JOIN utl_contatto ON ((utl_contatto.id = con_organizzazione_contatto.id_contatto)))
                     LEFT JOIN vol_sede ON (((vol_sede.id_organizzazione = vol_organizzazione.id) AND (vol_sede.tipo = 'Sede Legale'::vol_sede_tipo))))
                     LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                  WHERE ((utl_contatto.contatto IS NOT NULL) AND (vol_organizzazione.stato_iscrizione = 3))
                UNION
                 SELECT DISTINCT ON (con_mas_rubrica_contatto.id) con_mas_rubrica_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_mas_rubrica_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_mas_rubrica_contatto'::text AS contatto_type,
                    concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
                    con_mas_rubrica_contatto.type AS tipo_contatto,
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
                    utl_contatto.vendor
                   FROM ((((((con_mas_rubrica_contatto
                     LEFT JOIN mas_rubrica ON ((con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id)))
                     LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = mas_rubrica.id_anagrafica)))
                     LEFT JOIN utl_contatto ON ((con_mas_rubrica_contatto.id_contatto = utl_contatto.id)))
                     LEFT JOIN utl_indirizzo ON ((utl_indirizzo.id = mas_rubrica.id_indirizzo)))
                     LEFT JOIN loc_comune ON ((loc_comune.id = utl_indirizzo.id_comune)))
                     LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
                  WHERE (utl_contatto.id IS NOT NULL)
                UNION
                 SELECT DISTINCT ON (con_operatore_pc_contatto.id) con_operatore_pc_contatto.id_contatto,
                    utl_contatto.contatto AS valore_contatto,
                    con_operatore_pc_contatto.use_type,
                    utl_contatto.check_mobile,
                    utl_contatto.check_predefinito,
                    'con_operatore_pc_contatto'::text AS contatto_type,
                    concat(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) AS valore_riferimento,
                    con_operatore_pc_contatto.type AS tipo_contatto,
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
                    utl_contatto.vendor
                   FROM (((con_operatore_pc_contatto
                     LEFT JOIN utl_operatore_pc ON ((con_operatore_pc_contatto.id_operatore_pc = utl_operatore_pc.id)))
                     LEFT JOIN utl_contatto ON ((con_operatore_pc_contatto.id_contatto = utl_contatto.id)))
                     LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = utl_operatore_pc.id_anagrafica)))
                  WHERE (utl_contatto.id IS NOT NULL)) t
             LEFT JOIN con_view_rubrica_everbridge_ext_ids ON (((con_view_rubrica_everbridge_ext_ids.contatto)::text = concat(t.id_contatto, '_', t.contatto_type))));")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_150419_alter_view_rubrica_for_note cannot be reverted.\n";

        return false;
    }
    */
}
