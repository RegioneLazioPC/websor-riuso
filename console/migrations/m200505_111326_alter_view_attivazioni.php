<?php

use yii\db\Migration;

/**
 * Class m200505_111326_alter_view_attivazioni
 */
class m200505_111326_alter_view_attivazioni extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_report_attivazioni AS
            SELECT i.id AS id_attivazione,
            e.id AS id_evento,
            i.created_at,
            i.closed_at,
            (((date_part('day'::text, (i.closed_at - i.created_at)) * (24)::double precision) + (date_part('hour'::text, (i.closed_at - i.created_at)) * (60)::double precision)) + date_part('minute'::text, (i.closed_at - i.created_at))) AS durata,
            lpad((date_part('month'::text, i.created_at))::text, 2, '0'::text) AS mese,
            date_part('month'::text, i.created_at) AS mese_int,
            date_part('year'::text, i.created_at) AS anno,
            e.num_protocollo,
                CASE
                    WHEN (t.tipologia IS NOT NULL) THEN t.tipologia
                    ELSE 'Sos'::character varying
                END AS tipologia,
            st.tipologia AS sottotipologia,
            g.id AS id_gestore,
            g.descrizione AS gestore,
                CASE
                    WHEN (e.has_coc = 1) THEN 'Si'::text
                    ELSE 'No'::text
                END AS coc,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS indirizzo,
            c.id AS id_comune,
            c.comune,
            p.id AS id_provincia,
            p.provincia,
            p.sigla AS provincia_sigla,
            a.id AS id_automezzo,
            a.targa,
            ta.id AS id_tipo_automezzo,
            ta.descrizione AS tipo_automezzo,
            attr.id AS id_attrezzatura,
            attr.modello AS modello_attrezzatura,
            attrta.id AS id_tipo_attrezzatura,
            attrta.descrizione AS tipo_attrezzatura,
            v.ref_id AS num_elenco_territoriale,
            v.id AS id_organizzazione,
            v.denominazione AS organizzazione,
            concat(s.indirizzo, ' ', cs.comune, ' (', ps.sigla, ')') AS indirizzo_sede,
            s.tipo AS tipo_sede,
                CASE
                    WHEN (i.stato = 0) THEN 'In attesa di conferma'::text
                    WHEN (i.stato = 1) THEN 'Confermato'::text
                    WHEN (i.stato = 2) THEN 'Rifiutato'::text
                    WHEN (i.stato = 3) THEN 'Chiuso'::text
                    ELSE '-'::text
                END AS stato,
                CASE
                    WHEN (i.motivazione_rifiuto = 0) THEN 'FUORI ORARIO'::text
                    WHEN (i.motivazione_rifiuto = 1) THEN 'NON RISPONDE'::text
                    WHEN (i.motivazione_rifiuto = 2) THEN 'MEZZO NON DISPONIBILE'::text
                    WHEN (i.motivazione_rifiuto = 3) THEN 'SQUADRA NON DISPONIBILE'::text
                    WHEN (i.motivazione_rifiuto = 4) THEN 'IMPEGNATA CON ALTRO ENTE'::text
                    WHEN (i.motivazione_rifiuto = 5) THEN 'ALTRO'::text
                    ELSE '-'::text
                END AS motivazione_rifiuto,
            i.note,
            e.lat,
            e.lon,
            st_makepoint(e.lon, e.lat) AS geom,
            array_to_string(array_agg(DISTINCT agg_a.descrizione), ', '::text) AS aggregatore_automezzi,
            array_to_string(array_agg(DISTINCT agg_attr.descrizione), ', '::text) AS aggregatore_attrezzature
           FROM ((((((((((((((((((utl_ingaggio i
             LEFT JOIN utl_evento e ON ((e.id = i.idevento)))
             LEFT JOIN utl_tipologia t ON ((t.id = e.tipologia_evento)))
             LEFT JOIN utl_tipologia st ON ((st.id = e.sottotipologia_evento)))
             LEFT JOIN evt_gestore_evento g ON ((g.id = e.id_gestore_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura attr ON ((attr.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo attrta ON ((attrta.id = attr.idtipo)))
             LEFT JOIN vol_organizzazione v ON ((v.id = i.idorganizzazione)))
             LEFT JOIN vol_sede s ON ((s.id = i.idsede)))
             LEFT JOIN loc_comune cs ON ((cs.id = s.comune)))
             LEFT JOIN loc_provincia ps ON ((ps.id = cs.id_provincia)))
             LEFT JOIN con_aggregatore_tipologie_tipologie c_agga ON ((c_agga.id_tipo_automezzo = ta.id)))
             LEFT JOIN con_aggregatore_tipologie_tipologie c_aggattr ON ((c_aggattr.id_tipo_attrezzatura = ta.id)))
             LEFT JOIN utl_aggregatore_tipologie agg_a ON ((agg_a.id = c_agga.id_aggregatore)))
             LEFT JOIN utl_aggregatore_tipologie agg_attr ON ((agg_attr.id = c_aggattr.id_aggregatore)))
            WHERE i.idevento is not null
          GROUP BY i.id, e.id, t.id, st.id, g.id, c.id, p.id, a.id, ta.id, attr.id, attrta.id, v.id, s.id, cs.id, ps.id
        UNION
         SELECT NULL::integer AS id_attivazione,
            e.id AS id_evento,
            e.dataora_evento AS created_at,
            e.closed_at,
            (((date_part('day'::text, (e.closed_at - e.dataora_evento)) * (24)::double precision) + (date_part('hour'::text, (e.closed_at - e.dataora_evento)) * (60)::double precision)) + date_part('minute'::text, (e.closed_at - e.dataora_evento))) AS durata,
            lpad((date_part('month'::text, e.dataora_evento))::text, 2, '0'::text) AS mese,
            date_part('month'::text, e.dataora_evento) AS mese_int,
            date_part('year'::text, e.dataora_evento) AS anno,
            e.num_protocollo,
                CASE
                    WHEN (t.tipologia IS NOT NULL) THEN t.tipologia
                    ELSE 'Sos'::character varying
                END AS tipologia,
            st.tipologia AS sottotipologia,
            g.id AS id_gestore,
            g.descrizione AS gestore,
                CASE
                    WHEN (e.has_coc = 1) THEN 'Si'::text
                    ELSE 'No'::text
                END AS coc,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS indirizzo,
            c.id AS id_comune,
            c.comune,
            p.id AS id_provincia,
            p.provincia,
            p.sigla AS provincia_sigla,
            NULL::integer AS id_automezzo,
            NULL::character varying AS targa,
            NULL::integer AS id_tipo_automezzo,
            NULL::character varying AS tipo_automezzo,
            NULL::integer AS id_attrezzatura,
            NULL::character varying AS modello_attrezzatura,
            NULL::integer AS id_tipo_attrezzatura,
            NULL::character varying AS tipo_attrezzatura,
            NULL::integer AS num_elenco_territoriale,
            NULL::integer AS id_organizzazione,
            NULL::character varying AS organizzazione,
            NULL::text AS indirizzo_sede,
            NULL::vol_sede_tipo AS tipo_sede,
            '-'::text AS stato,
            '-'::text AS motivazione_rifiuto,
            NULL::text AS note,
            e.lat,
            e.lon,
            st_makepoint(e.lon, e.lat) AS geom,
            ''::text AS aggregatore_automezzi,
            ''::text AS aggregatore_attrezzature
           FROM (((((utl_evento e
             LEFT JOIN utl_tipologia t ON ((t.id = e.tipologia_evento)))
             LEFT JOIN utl_tipologia st ON ((st.id = e.sottotipologia_evento)))
             LEFT JOIN evt_gestore_evento g ON ((g.id = e.id_gestore_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
          WHERE (( SELECT count(u.id) AS count
                   FROM utl_ingaggio u
                  WHERE (u.idevento = e.id)) = 0 AND (e.id is not null));
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_report_attivazioni AS
            SELECT i.id AS id_attivazione,
            e.id AS id_evento,
            i.created_at,
            i.closed_at,
            (((date_part('day'::text, (i.closed_at - i.created_at)) * (24)::double precision) + (date_part('hour'::text, (i.closed_at - i.created_at)) * (60)::double precision)) + date_part('minute'::text, (i.closed_at - i.created_at))) AS durata,
            lpad((date_part('month'::text, i.created_at))::text, 2, '0'::text) AS mese,
            date_part('month'::text, i.created_at) AS mese_int,
            date_part('year'::text, i.created_at) AS anno,
            e.num_protocollo,
                CASE
                    WHEN (t.tipologia IS NOT NULL) THEN t.tipologia
                    ELSE 'Sos'::character varying
                END AS tipologia,
            st.tipologia AS sottotipologia,
            g.id AS id_gestore,
            g.descrizione AS gestore,
                CASE
                    WHEN (e.has_coc = 1) THEN 'Si'::text
                    ELSE 'No'::text
                END AS coc,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS indirizzo,
            c.id AS id_comune,
            c.comune,
            p.id AS id_provincia,
            p.provincia,
            p.sigla AS provincia_sigla,
            a.id AS id_automezzo,
            a.targa,
            ta.id AS id_tipo_automezzo,
            ta.descrizione AS tipo_automezzo,
            attr.id AS id_attrezzatura,
            attr.modello AS modello_attrezzatura,
            attrta.id AS id_tipo_attrezzatura,
            attrta.descrizione AS tipo_attrezzatura,
            v.ref_id AS num_elenco_territoriale,
            v.id AS id_organizzazione,
            v.denominazione AS organizzazione,
            concat(s.indirizzo, ' ', cs.comune, ' (', ps.sigla, ')') AS indirizzo_sede,
            s.tipo AS tipo_sede,
                CASE
                    WHEN (i.stato = 0) THEN 'In attesa di conferma'::text
                    WHEN (i.stato = 1) THEN 'Confermato'::text
                    WHEN (i.stato = 2) THEN 'Rifiutato'::text
                    WHEN (i.stato = 3) THEN 'Chiuso'::text
                    ELSE '-'::text
                END AS stato,
                CASE
                    WHEN (i.motivazione_rifiuto = 0) THEN 'FUORI ORARIO'::text
                    WHEN (i.motivazione_rifiuto = 1) THEN 'NON RISPONDE'::text
                    WHEN (i.motivazione_rifiuto = 2) THEN 'MEZZO NON DISPONIBILE'::text
                    WHEN (i.motivazione_rifiuto = 3) THEN 'SQUADRA NON DISPONIBILE'::text
                    WHEN (i.motivazione_rifiuto = 4) THEN 'IMPEGNATA CON ALTRO ENTE'::text
                    WHEN (i.motivazione_rifiuto = 5) THEN 'ALTRO'::text
                    ELSE '-'::text
                END AS motivazione_rifiuto,
            i.note,
            e.lat,
            e.lon,
            st_makepoint(e.lon, e.lat) AS geom,
            array_to_string(array_agg(DISTINCT agg_a.descrizione), ', '::text) AS aggregatore_automezzi,
            array_to_string(array_agg(DISTINCT agg_attr.descrizione), ', '::text) AS aggregatore_attrezzature
           FROM ((((((((((((((((((utl_ingaggio i
             LEFT JOIN utl_evento e ON ((e.id = i.idevento)))
             LEFT JOIN utl_tipologia t ON ((t.id = e.tipologia_evento)))
             LEFT JOIN utl_tipologia st ON ((st.id = e.sottotipologia_evento)))
             LEFT JOIN evt_gestore_evento g ON ((g.id = e.id_gestore_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura attr ON ((attr.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo attrta ON ((attrta.id = attr.idtipo)))
             LEFT JOIN vol_organizzazione v ON ((v.id = i.idorganizzazione)))
             LEFT JOIN vol_sede s ON ((s.id = i.idsede)))
             LEFT JOIN loc_comune cs ON ((cs.id = s.comune)))
             LEFT JOIN loc_provincia ps ON ((ps.id = cs.id_provincia)))
             LEFT JOIN con_aggregatore_tipologie_tipologie c_agga ON ((c_agga.id_tipo_automezzo = ta.id)))
             LEFT JOIN con_aggregatore_tipologie_tipologie c_aggattr ON ((c_aggattr.id_tipo_attrezzatura = ta.id)))
             LEFT JOIN utl_aggregatore_tipologie agg_a ON ((agg_a.id = c_agga.id_aggregatore)))
             LEFT JOIN utl_aggregatore_tipologie agg_attr ON ((agg_attr.id = c_aggattr.id_aggregatore)))
          GROUP BY i.id, e.id, t.id, st.id, g.id, c.id, p.id, a.id, ta.id, attr.id, attrta.id, v.id, s.id, cs.id, ps.id
        UNION
         SELECT NULL::integer AS id_attivazione,
            e.id AS id_evento,
            e.dataora_evento AS created_at,
            e.closed_at,
            (((date_part('day'::text, (e.closed_at - e.dataora_evento)) * (24)::double precision) + (date_part('hour'::text, (e.closed_at - e.dataora_evento)) * (60)::double precision)) + date_part('minute'::text, (e.closed_at - e.dataora_evento))) AS durata,
            lpad((date_part('month'::text, e.dataora_evento))::text, 2, '0'::text) AS mese,
            date_part('month'::text, e.dataora_evento) AS mese_int,
            date_part('year'::text, e.dataora_evento) AS anno,
            e.num_protocollo,
                CASE
                    WHEN (t.tipologia IS NOT NULL) THEN t.tipologia
                    ELSE 'Sos'::character varying
                END AS tipologia,
            st.tipologia AS sottotipologia,
            g.id AS id_gestore,
            g.descrizione AS gestore,
                CASE
                    WHEN (e.has_coc = 1) THEN 'Si'::text
                    ELSE 'No'::text
                END AS coc,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS indirizzo,
            c.id AS id_comune,
            c.comune,
            p.id AS id_provincia,
            p.provincia,
            p.sigla AS provincia_sigla,
            NULL::integer AS id_automezzo,
            NULL::character varying AS targa,
            NULL::integer AS id_tipo_automezzo,
            NULL::character varying AS tipo_automezzo,
            NULL::integer AS id_attrezzatura,
            NULL::character varying AS modello_attrezzatura,
            NULL::integer AS id_tipo_attrezzatura,
            NULL::character varying AS tipo_attrezzatura,
            NULL::integer AS num_elenco_territoriale,
            NULL::integer AS id_organizzazione,
            NULL::character varying AS organizzazione,
            NULL::text AS indirizzo_sede,
            NULL::vol_sede_tipo AS tipo_sede,
            '-'::text AS stato,
            '-'::text AS motivazione_rifiuto,
            NULL::text AS note,
            e.lat,
            e.lon,
            st_makepoint(e.lon, e.lat) AS geom,
            ''::text AS aggregatore_automezzi,
            ''::text AS aggregatore_attrezzature
           FROM (((((utl_evento e
             LEFT JOIN utl_tipologia t ON ((t.id = e.tipologia_evento)))
             LEFT JOIN utl_tipologia st ON ((st.id = e.sottotipologia_evento)))
             LEFT JOIN evt_gestore_evento g ON ((g.id = e.id_gestore_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
          WHERE (( SELECT count(u.id) AS count
                   FROM utl_ingaggio u
                  WHERE (u.idevento = e.id)) = 0);

            ")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200505_111326_alter_view_attivazioni cannot be reverted.\n";

        return false;
    }
    */
}
