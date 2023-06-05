<?php

/*
DEBUG VALUES

    routing.view_ingaggio_organizzazioni
         SELECT ingaggio.idevento,
            ingaggio.idorganizzazione,
            array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(aut.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE((aut.ingaggio_stato)::text, ''::text), 'ingaggio_created_at', COALESCE((aut.ingaggio_created_at)::text, ''::text), 'ingaggio_updated_at', COALESCE((aut.ingaggio_updated_at)::text, ''::text), 'ingaggio_closed_at', COALESCE((aut.ingaggio_closed_at)::text, ''::text), 'id', COALESCE((aut.id)::text, ''::text), 'targa', COALESCE((aut.targa)::text, ''::text), 'data_immatricolazione', COALESCE((aut.data_immatricolazione)::text, ''::text), 'idsquadra', COALESCE((aut.idsquadra)::text, ''::text), 'classe', COALESCE((aut.classe)::text, ''::text), 'sottoclasse', COALESCE((aut.sottoclasse)::text, ''::text), 'modello', COALESCE((aut.modello)::text, ''::text), 'idtipo', COALESCE((aut.idtipo)::text, ''::text), 'capacita', COALESCE((aut.capacita)::text, ''::text), 'disponibilita', COALESCE((aut.disponibilita)::text, ''::text), 'idorganizzazione', COALESCE((aut.idorganizzazione)::text, ''::text), 'idsede', COALESCE((aut.idsede)::text, ''::text), 'tempo_attivazione', COALESCE((aut.tempo_attivazione)::text, ''::text), 'allestimento', COALESCE((aut.allestimento)::text, ''::text), 'engaged', COALESCE((aut.engaged)::text, ''::text)) AS automezzo
                   FROM ( SELECT a.id,
                            a.targa,
                            a.data_immatricolazione,
                            a.idsquadra,
                            a.classe,
                            a.sottoclasse,
                            a.modello,
                            a.idtipo,
                            a.capacita,
                            a.disponibilita,
                            a.idorganizzazione,
                            a.idsede,
                            a.tempo_attivazione,
                            a.allestimento,
                            a.engaged,
                            i.note AS ingaggio_note,
                            i.stato AS ingaggio_stato,
                            i.created_at AS ingaggio_created_at,
                            i.updated_at AS ingaggio_updated_at,
                            i.closed_at AS ingaggio_closed_at
                           FROM (utl_automezzo a
                             JOIN utl_ingaggio i ON (((i.idautomezzo = a.id) AND (i.idevento = ingaggio.idevento) AND (i.idorganizzazione = ingaggio.idorganizzazione))))) aut
                  WHERE (aut.id = ANY (array_agg(ingaggio.idautomezzo))))) AS automezzi,
            array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(atr.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE((atr.ingaggio_stato)::text, ''::text), 'ingaggio_created_at', COALESCE((atr.ingaggio_created_at)::text, ''::text), 'ingaggio_updated_at', COALESCE((atr.ingaggio_updated_at)::text, ''::text), 'ingaggio_closed_at', COALESCE((atr.ingaggio_closed_at)::text, ''::text), 'id', COALESCE((atr.id)::text, ''::text), 'idtipo', COALESCE((atr.idtipo)::text, ''::text), 'classe', COALESCE((atr.classe)::text, ''::text), 'sottoclasse', COALESCE((atr.sottoclasse)::text, ''::text), 'modello', COALESCE((atr.modello)::text, ''::text), 'capacita', COALESCE((atr.capacita)::text, ''::text), 'unita', COALESCE((atr.unita)::text, ''::text), 'idorganizzazione', COALESCE((atr.idorganizzazione)::text, ''::text), 'idsede', COALESCE((atr.idsede)::text, ''::text), 'idautomezzo', COALESCE((atr.idautomezzo)::text, ''::text), 'tempo_attivazione', COALESCE((atr.tempo_attivazione)::text, ''::text), 'allestimento', COALESCE((atr.allestimento)::text, ''::text), 'engaged', COALESCE((atr.engaged)::text, ''::text)) AS attrezzatura
                   FROM ( SELECT a.id,
                            a.idtipo,
                            a.classe,
                            a.sottoclasse,
                            a.modello,
                            a.capacita,
                            a.unita,
                            a.idorganizzazione,
                            a.idsede,
                            a.idautomezzo,
                            a.tempo_attivazione,
                            a.allestimento,
                            a.engaged,
                            i.note AS ingaggio_note,
                            i.stato AS ingaggio_stato,
                            i.created_at AS ingaggio_created_at,
                            i.updated_at AS ingaggio_updated_at,
                            i.closed_at AS ingaggio_closed_at
                           FROM (utl_attrezzatura a
                             JOIN utl_ingaggio i ON (((i.idattrezzatura = a.id) AND (i.idevento = ingaggio.idevento) AND (i.idorganizzazione = ingaggio.idorganizzazione))))) atr
                  WHERE (atr.id = ANY (array_agg(ingaggio.idattrezzatura))))) AS attrezzature
           FROM utl_ingaggio ingaggio
          WHERE (ingaggio.idorganizzazione IS NOT NULL)
          GROUP BY ingaggio.idorganizzazione, ingaggio.idevento;
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            utl_automezzo_tipo.id AS tipo_automezzo_id,
            utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (((utl_automezzo.disponibilita)::integer = 1) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_attrezzatura.idautomezzo IS NULL) AND (utl_attrezzatura.disponibilita = 1) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3);





    view_report_attivazioni_volontari
     SELECT i.id AS id_attivazione,
        ana.nome,
        ana.cognome,
        ana.codfiscale,
        vol.datore_di_lavoro,
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
       FROM (((((((((((((((((((((con_volontario_ingaggio cv
         LEFT JOIN utl_ingaggio i ON ((i.id = cv.id_ingaggio)))
         LEFT JOIN vol_volontario vol ON ((vol.id = cv.id_volontario)))
         LEFT JOIN utl_anagrafica ana ON ((ana.id = vol.id_anagrafica)))
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
      WHERE (i.idevento IS NOT NULL)
      GROUP BY cv.id, ana.id, vol.id, i.id, e.id, t.id, st.id, g.id, c.id, p.id, a.id, ta.id, attr.id, attrta.id, v.id, s.id, cs.id, ps.id;

    view_report_attivazioni
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
          WHERE (i.idevento IS NOT NULL)
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
          WHERE ((( SELECT count(u.id) AS count
                   FROM utl_ingaggio u
                  WHERE (u.idevento = e.id)) = 0) AND (e.id IS NOT NULL));
 
    view_volontari_attivazioni
         SELECT concat(i.id, '_', v.id) AS id,
            o.ref_id AS num_elenco_territoriale,
            o.denominazione,
            e.num_protocollo AS protocollo_evento,
            NULL::character varying AS protocollo_fronte,
            e.id AS id_evento,
            NULL::integer AS id_fronte,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS localita,
            c.comune,
            p.sigla AS provincia,
            tp.id AS id_tipologia,
            tp.tipologia,
            stp.id AS id_sottotipologia,
            stp.tipologia AS sottotipologia,
            a.id AS id_mezzo,
            ta.descrizione AS mezzo,
            a.targa,
            att.id AS id_attrezzatura,
            tatt.descrizione AS attrezzatura,
            att.modello,
                CASE
                    WHEN (a.id IS NOT NULL) THEN concat(ta.descrizione, ', targa: ', a.targa)
                    WHEN (att.id IS NOT NULL) THEN concat(tatt.descrizione, ', modello: ', att.modello)
                    ELSE ''::text
                END AS full_mezzo,
            i.id AS id_attivazione,
            i.created_at AS creazione,
            i.closed_at AS chiusura,
            i.stato,
            v.id AS id_volontario,
            ana.id AS id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
           FROM ((((((((((((((utl_evento e
             LEFT JOIN utl_evento eg ON ((eg.id = e.idparent)))
             LEFT JOIN utl_tipologia tp ON ((tp.id = e.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_tipologia stp ON ((stp.id = e.sottotipologia_evento)))
             LEFT JOIN utl_ingaggio i ON ((i.idevento = e.id)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura att ON ((att.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo tatt ON ((tatt.id = att.idtipo)))
             LEFT JOIN con_volontario_ingaggio ci ON ((ci.id_ingaggio = i.id)))
             LEFT JOIN vol_volontario v ON ((v.id = ci.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione o ON ((o.id = i.idorganizzazione)))
          WHERE ((ana.id IS NOT NULL) AND (e.idparent IS NULL))
        UNION ALL
         SELECT concat(i.id, '_', v.id) AS id,
            o.ref_id AS num_elenco_territoriale,
            o.denominazione,
            eg.num_protocollo AS protocollo_evento,
            e.num_protocollo AS protocollo_fronte,
            eg.id AS id_evento,
            e.id AS id_fronte,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS localita,
            c.comune,
            p.sigla AS provincia,
            tp.id AS id_tipologia,
            tp.tipologia,
            stp.id AS id_sottotipologia,
            stp.tipologia AS sottotipologia,
            a.id AS id_mezzo,
            ta.descrizione AS mezzo,
            a.targa,
            att.id AS id_attrezzatura,
            tatt.descrizione AS attrezzatura,
            att.modello,
                CASE
                    WHEN (a.id IS NOT NULL) THEN concat(ta.descrizione, ', targa: ', a.targa)
                    WHEN (att.id IS NOT NULL) THEN concat(tatt.descrizione, ', modello: ', att.modello)
                    ELSE ''::text
                END AS full_mezzo,
            i.id AS id_attivazione,
            i.created_at AS creazione,
            i.closed_at AS chiusura,
            i.stato,
            v.id AS id_volontario,
            ana.id AS id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
           FROM ((((((((((((((utl_evento e
             LEFT JOIN utl_evento eg ON ((eg.id = e.idparent)))
             LEFT JOIN utl_tipologia tp ON ((tp.id = e.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_tipologia stp ON ((stp.id = e.sottotipologia_evento)))
             LEFT JOIN utl_ingaggio i ON ((i.idevento = e.id)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura att ON ((att.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo tatt ON ((tatt.id = att.idtipo)))
             LEFT JOIN con_volontario_ingaggio ci ON ((ci.id_ingaggio = i.id)))
             LEFT JOIN vol_volontario v ON ((v.id = ci.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione o ON ((o.id = i.idorganizzazione)))
          WHERE ((ana.id IS NOT NULL) AND (e.idparent IS NOT NULL));
 
    view_organizzazioni
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            utl_automezzo.meta AS ref_meta,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            utl_automezzo_tipo.id AS tipo_automezzo_id,
            utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM (((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (((utl_automezzo.disponibilita)::text = '1'::text) AND (vol_organizzazione.stato_iscrizione = 3))
          GROUP BY utl_automezzo.id, utl_automezzo_tipo.id, vol_sede.id, vol_organizzazione.id, utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            utl_attrezzatura.meta AS ref_meta,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_attrezzatura.idautomezzo IS NULL) AND (utl_attrezzatura.disponibilita = 1) AND (vol_organizzazione.stato_iscrizione = 3))
          GROUP BY utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_sede.id, vol_organizzazione.id, vol_organizzazione.ref_id
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            NULL::jsonb AS ref_meta,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3)
          GROUP BY vol_sede.id, vol_organizzazione.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id;

    geo_datas
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo
           FROM ((((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (utl_attrezzatura.idautomezzo IS NULL)
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)));
*/


use yii\db\Migration;

/**
 * Class m200819_110548_alter_data_length
 */
class m200819_110548_alter_data_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        
        Yii::$app->db->createCommand("DROP VIEW routing.view_ingaggio_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW routing.view_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni_volontari")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_volontari_attivazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW geo_datas")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN allestimento TYPE varchar")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN allestimento TYPE varchar")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN classe TYPE varchar")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN sottoclasse TYPE varchar")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN modello TYPE varchar")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN classe TYPE varchar")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN sottoclasse TYPE varchar")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN modello TYPE varchar")->execute();


        Yii::$app->db->createCommand("CREATE VIEW routing.view_organizzazioni AS 
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            utl_automezzo_tipo.id AS tipo_automezzo_id,
            utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (((utl_automezzo.disponibilita)::integer = 1) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_attrezzatura.idautomezzo IS NULL) AND (utl_attrezzatura.disponibilita = 1) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3);")->execute();

        Yii::$app->db->createCommand("CREATE VIEW routing.view_ingaggio_organizzazioni AS 
                     SELECT 
                        ingaggio.idevento,
                        ingaggio.idorganizzazione,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(aut.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE((aut.ingaggio_stato)::text, ''::text), 'ingaggio_created_at', COALESCE((aut.ingaggio_created_at)::text, ''::text), 'ingaggio_updated_at', COALESCE((aut.ingaggio_updated_at)::text, ''::text), 'ingaggio_closed_at', COALESCE((aut.ingaggio_closed_at)::text, ''::text), 'id', COALESCE((aut.id)::text, ''::text), 'targa', COALESCE((aut.targa)::text, ''::text), 'data_immatricolazione', COALESCE((aut.data_immatricolazione)::text, ''::text), 'idsquadra', COALESCE((aut.idsquadra)::text, ''::text), 'classe', COALESCE((aut.classe)::text, ''::text), 'sottoclasse', COALESCE((aut.sottoclasse)::text, ''::text), 'modello', COALESCE((aut.modello)::text, ''::text), 'idtipo', COALESCE((aut.idtipo)::text, ''::text), 'capacita', COALESCE((aut.capacita)::text, ''::text), 'disponibilita', COALESCE((aut.disponibilita)::text, ''::text), 'idorganizzazione', COALESCE((aut.idorganizzazione)::text, ''::text), 'idsede', COALESCE((aut.idsede)::text, ''::text), 'tempo_attivazione', COALESCE((aut.tempo_attivazione)::text, ''::text), 'allestimento', COALESCE((aut.allestimento)::text, ''::text), 'engaged', COALESCE((aut.engaged)::text, ''::text)) AS automezzo
                               FROM ( SELECT a.id,
                                        a.targa,
                                        a.data_immatricolazione,
                                        a.idsquadra,
                                        a.classe,
                                        a.sottoclasse,
                                        a.modello,
                                        a.idtipo,
                                        a.capacita,
                                        a.disponibilita,
                                        a.idorganizzazione,
                                        a.idsede,
                                        a.tempo_attivazione,
                                        a.allestimento,
                                        a.engaged,
                                        i.note AS ingaggio_note,
                                        i.stato AS ingaggio_stato,
                                        i.created_at AS ingaggio_created_at,
                                        i.updated_at AS ingaggio_updated_at,
                                        i.closed_at AS ingaggio_closed_at
                                       FROM (utl_automezzo a
                                         JOIN utl_ingaggio i ON (((i.idautomezzo = a.id) AND (i.idevento = ingaggio.idevento) AND (i.idorganizzazione = ingaggio.idorganizzazione))))) aut
                              WHERE (aut.id = ANY (array_agg(ingaggio.idautomezzo))))) AS automezzi,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(atr.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE((atr.ingaggio_stato)::text, ''::text), 'ingaggio_created_at', COALESCE((atr.ingaggio_created_at)::text, ''::text), 'ingaggio_updated_at', COALESCE((atr.ingaggio_updated_at)::text, ''::text), 'ingaggio_closed_at', COALESCE((atr.ingaggio_closed_at)::text, ''::text), 'id', COALESCE((atr.id)::text, ''::text), 'idtipo', COALESCE((atr.idtipo)::text, ''::text), 'classe', COALESCE((atr.classe)::text, ''::text), 'sottoclasse', COALESCE((atr.sottoclasse)::text, ''::text), 'modello', COALESCE((atr.modello)::text, ''::text), 'capacita', COALESCE((atr.capacita)::text, ''::text), 'unita', COALESCE((atr.unita)::text, ''::text), 'idorganizzazione', COALESCE((atr.idorganizzazione)::text, ''::text), 'idsede', COALESCE((atr.idsede)::text, ''::text), 'idautomezzo', COALESCE((atr.idautomezzo)::text, ''::text), 'tempo_attivazione', COALESCE((atr.tempo_attivazione)::text, ''::text), 'allestimento', COALESCE((atr.allestimento)::text, ''::text), 'engaged', COALESCE((atr.engaged)::text, ''::text)) AS attrezzatura
                               FROM ( SELECT a.id,
                                        a.idtipo,
                                        a.classe,
                                        a.sottoclasse,
                                        a.modello,
                                        a.capacita,
                                        a.unita,
                                        a.idorganizzazione,
                                        a.idsede,
                                        a.idautomezzo,
                                        a.tempo_attivazione,
                                        a.allestimento,
                                        a.engaged,
                                        i.note AS ingaggio_note,
                                        i.stato AS ingaggio_stato,
                                        i.created_at AS ingaggio_created_at,
                                        i.updated_at AS ingaggio_updated_at,
                                        i.closed_at AS ingaggio_closed_at
                                       FROM (utl_attrezzatura a
                                         JOIN utl_ingaggio i ON (((i.idattrezzatura = a.id) AND (i.idevento = ingaggio.idevento) AND (i.idorganizzazione = ingaggio.idorganizzazione))))) atr
                              WHERE (atr.id = ANY (array_agg(ingaggio.idattrezzatura))))) AS attrezzature
                       FROM utl_ingaggio ingaggio
                      WHERE (ingaggio.idorganizzazione IS NOT NULL)
                      GROUP BY ingaggio.idorganizzazione, ingaggio.idevento;")->execute();



        Yii::$app->db->createCommand("CREATE VIEW view_report_attivazioni_volontari AS 
        SELECT i.id AS id_attivazione,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            vol.datore_di_lavoro,
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
           FROM (((((((((((((((((((((con_volontario_ingaggio cv
             LEFT JOIN utl_ingaggio i ON ((i.id = cv.id_ingaggio)))
             LEFT JOIN vol_volontario vol ON ((vol.id = cv.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = vol.id_anagrafica)))
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
          WHERE (i.idevento IS NOT NULL)
          GROUP BY cv.id, ana.id, vol.id, i.id, e.id, t.id, st.id, g.id, c.id, p.id, a.id, ta.id, attr.id, attrta.id, v.id, s.id, cs.id, ps.id;")->execute();

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
          WHERE (i.idevento IS NOT NULL)
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
          WHERE ((( SELECT count(u.id) AS count
                   FROM utl_ingaggio u
                  WHERE (u.idevento = e.id)) = 0) AND (e.id IS NOT NULL));")->execute();


        Yii::$app->db->createCommand("CREATE VIEW view_volontari_attivazioni AS 
         SELECT concat(i.id, '_', v.id) AS id,
            o.ref_id AS num_elenco_territoriale,
            o.denominazione,
            e.num_protocollo AS protocollo_evento,
            NULL::character varying AS protocollo_fronte,
            e.id AS id_evento,
            NULL::integer AS id_fronte,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS localita,
            c.comune,
            p.sigla AS provincia,
            tp.id AS id_tipologia,
            tp.tipologia,
            stp.id AS id_sottotipologia,
            stp.tipologia AS sottotipologia,
            a.id AS id_mezzo,
            ta.descrizione AS mezzo,
            a.targa,
            att.id AS id_attrezzatura,
            tatt.descrizione AS attrezzatura,
            att.modello,
                CASE
                    WHEN (a.id IS NOT NULL) THEN concat(ta.descrizione, ', targa: ', a.targa)
                    WHEN (att.id IS NOT NULL) THEN concat(tatt.descrizione, ', modello: ', att.modello)
                    ELSE ''::text
                END AS full_mezzo,
            i.id AS id_attivazione,
            i.created_at AS creazione,
            i.closed_at AS chiusura,
            i.stato,
            v.id AS id_volontario,
            ana.id AS id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
           FROM ((((((((((((((utl_evento e
             LEFT JOIN utl_evento eg ON ((eg.id = e.idparent)))
             LEFT JOIN utl_tipologia tp ON ((tp.id = e.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_tipologia stp ON ((stp.id = e.sottotipologia_evento)))
             LEFT JOIN utl_ingaggio i ON ((i.idevento = e.id)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura att ON ((att.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo tatt ON ((tatt.id = att.idtipo)))
             LEFT JOIN con_volontario_ingaggio ci ON ((ci.id_ingaggio = i.id)))
             LEFT JOIN vol_volontario v ON ((v.id = ci.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione o ON ((o.id = i.idorganizzazione)))
          WHERE ((ana.id IS NOT NULL) AND (e.idparent IS NULL))
        UNION ALL
         SELECT concat(i.id, '_', v.id) AS id,
            o.ref_id AS num_elenco_territoriale,
            o.denominazione,
            eg.num_protocollo AS protocollo_evento,
            e.num_protocollo AS protocollo_fronte,
            eg.id AS id_evento,
            e.id AS id_fronte,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS localita,
            c.comune,
            p.sigla AS provincia,
            tp.id AS id_tipologia,
            tp.tipologia,
            stp.id AS id_sottotipologia,
            stp.tipologia AS sottotipologia,
            a.id AS id_mezzo,
            ta.descrizione AS mezzo,
            a.targa,
            att.id AS id_attrezzatura,
            tatt.descrizione AS attrezzatura,
            att.modello,
                CASE
                    WHEN (a.id IS NOT NULL) THEN concat(ta.descrizione, ', targa: ', a.targa)
                    WHEN (att.id IS NOT NULL) THEN concat(tatt.descrizione, ', modello: ', att.modello)
                    ELSE ''::text
                END AS full_mezzo,
            i.id AS id_attivazione,
            i.created_at AS creazione,
            i.closed_at AS chiusura,
            i.stato,
            v.id AS id_volontario,
            ana.id AS id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
           FROM ((((((((((((((utl_evento e
             LEFT JOIN utl_evento eg ON ((eg.id = e.idparent)))
             LEFT JOIN utl_tipologia tp ON ((tp.id = e.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_tipologia stp ON ((stp.id = e.sottotipologia_evento)))
             LEFT JOIN utl_ingaggio i ON ((i.idevento = e.id)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura att ON ((att.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo tatt ON ((tatt.id = att.idtipo)))
             LEFT JOIN con_volontario_ingaggio ci ON ((ci.id_ingaggio = i.id)))
             LEFT JOIN vol_volontario v ON ((v.id = ci.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione o ON ((o.id = i.idorganizzazione)))
          WHERE ((ana.id IS NOT NULL) AND (e.idparent IS NOT NULL));")->execute();


        Yii::$app->db->createCommand("CREATE VIEW view_organizzazioni AS 
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            utl_automezzo.meta AS ref_meta,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            utl_automezzo_tipo.id AS tipo_automezzo_id,
            utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM (((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (((utl_automezzo.disponibilita)::text = '1'::text) AND (vol_organizzazione.stato_iscrizione = 3))
          GROUP BY utl_automezzo.id, utl_automezzo_tipo.id, vol_sede.id, vol_organizzazione.id, utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            utl_attrezzatura.meta AS ref_meta,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_attrezzatura.idautomezzo IS NULL) AND (utl_attrezzatura.disponibilita = 1) AND (vol_organizzazione.stato_iscrizione = 3))
          GROUP BY utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_sede.id, vol_organizzazione.id, vol_organizzazione.ref_id
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            NULL::jsonb AS ref_meta,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3)
          GROUP BY vol_sede.id, vol_organizzazione.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id;")->execute();


        Yii::$app->db->createCommand("CREATE VIEW geo_datas AS 
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo
           FROM ((((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (utl_attrezzatura.idautomezzo IS NULL)
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)));")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW routing.view_ingaggio_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW routing.view_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni_volontari")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_volontari_attivazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")->execute();
        Yii::$app->db->createCommand("DROP VIEW geo_datas")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN allestimento TYPE varchar(255)")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN allestimento TYPE varchar(255)")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN classe TYPE varchar(100)")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN sottoclasse TYPE varchar(100)")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_attrezzatura ALTER COLUMN modello TYPE varchar(100)")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN classe TYPE varchar(100)")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN sottoclasse TYPE varchar(100)")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_automezzo ALTER COLUMN modello TYPE varchar(100)")->execute();


        Yii::$app->db->createCommand("CREATE VIEW routing.view_organizzazioni AS 
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            utl_automezzo_tipo.id AS tipo_automezzo_id,
            utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (((utl_automezzo.disponibilita)::integer = 1) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_attrezzatura.idautomezzo IS NULL) AND (utl_attrezzatura.disponibilita = 1) AND (vol_organizzazione.stato_iscrizione = 3))
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3);")->execute();

        Yii::$app->db->createCommand("CREATE VIEW routing.view_ingaggio_organizzazioni AS 
                     SELECT 
                        ingaggio.idevento,
                        ingaggio.idorganizzazione,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(aut.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE((aut.ingaggio_stato)::text, ''::text), 'ingaggio_created_at', COALESCE((aut.ingaggio_created_at)::text, ''::text), 'ingaggio_updated_at', COALESCE((aut.ingaggio_updated_at)::text, ''::text), 'ingaggio_closed_at', COALESCE((aut.ingaggio_closed_at)::text, ''::text), 'id', COALESCE((aut.id)::text, ''::text), 'targa', COALESCE((aut.targa)::text, ''::text), 'data_immatricolazione', COALESCE((aut.data_immatricolazione)::text, ''::text), 'idsquadra', COALESCE((aut.idsquadra)::text, ''::text), 'classe', COALESCE((aut.classe)::text, ''::text), 'sottoclasse', COALESCE((aut.sottoclasse)::text, ''::text), 'modello', COALESCE((aut.modello)::text, ''::text), 'idtipo', COALESCE((aut.idtipo)::text, ''::text), 'capacita', COALESCE((aut.capacita)::text, ''::text), 'disponibilita', COALESCE((aut.disponibilita)::text, ''::text), 'idorganizzazione', COALESCE((aut.idorganizzazione)::text, ''::text), 'idsede', COALESCE((aut.idsede)::text, ''::text), 'tempo_attivazione', COALESCE((aut.tempo_attivazione)::text, ''::text), 'allestimento', COALESCE((aut.allestimento)::text, ''::text), 'engaged', COALESCE((aut.engaged)::text, ''::text)) AS automezzo
                               FROM ( SELECT a.id,
                                        a.targa,
                                        a.data_immatricolazione,
                                        a.idsquadra,
                                        a.classe,
                                        a.sottoclasse,
                                        a.modello,
                                        a.idtipo,
                                        a.capacita,
                                        a.disponibilita,
                                        a.idorganizzazione,
                                        a.idsede,
                                        a.tempo_attivazione,
                                        a.allestimento,
                                        a.engaged,
                                        i.note AS ingaggio_note,
                                        i.stato AS ingaggio_stato,
                                        i.created_at AS ingaggio_created_at,
                                        i.updated_at AS ingaggio_updated_at,
                                        i.closed_at AS ingaggio_closed_at
                                       FROM (utl_automezzo a
                                         JOIN utl_ingaggio i ON (((i.idautomezzo = a.id) AND (i.idevento = ingaggio.idevento) AND (i.idorganizzazione = ingaggio.idorganizzazione))))) aut
                              WHERE (aut.id = ANY (array_agg(ingaggio.idautomezzo))))) AS automezzi,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(atr.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE((atr.ingaggio_stato)::text, ''::text), 'ingaggio_created_at', COALESCE((atr.ingaggio_created_at)::text, ''::text), 'ingaggio_updated_at', COALESCE((atr.ingaggio_updated_at)::text, ''::text), 'ingaggio_closed_at', COALESCE((atr.ingaggio_closed_at)::text, ''::text), 'id', COALESCE((atr.id)::text, ''::text), 'idtipo', COALESCE((atr.idtipo)::text, ''::text), 'classe', COALESCE((atr.classe)::text, ''::text), 'sottoclasse', COALESCE((atr.sottoclasse)::text, ''::text), 'modello', COALESCE((atr.modello)::text, ''::text), 'capacita', COALESCE((atr.capacita)::text, ''::text), 'unita', COALESCE((atr.unita)::text, ''::text), 'idorganizzazione', COALESCE((atr.idorganizzazione)::text, ''::text), 'idsede', COALESCE((atr.idsede)::text, ''::text), 'idautomezzo', COALESCE((atr.idautomezzo)::text, ''::text), 'tempo_attivazione', COALESCE((atr.tempo_attivazione)::text, ''::text), 'allestimento', COALESCE((atr.allestimento)::text, ''::text), 'engaged', COALESCE((atr.engaged)::text, ''::text)) AS attrezzatura
                               FROM ( SELECT a.id,
                                        a.idtipo,
                                        a.classe,
                                        a.sottoclasse,
                                        a.modello,
                                        a.capacita,
                                        a.unita,
                                        a.idorganizzazione,
                                        a.idsede,
                                        a.idautomezzo,
                                        a.tempo_attivazione,
                                        a.allestimento,
                                        a.engaged,
                                        i.note AS ingaggio_note,
                                        i.stato AS ingaggio_stato,
                                        i.created_at AS ingaggio_created_at,
                                        i.updated_at AS ingaggio_updated_at,
                                        i.closed_at AS ingaggio_closed_at
                                       FROM (utl_attrezzatura a
                                         JOIN utl_ingaggio i ON (((i.idattrezzatura = a.id) AND (i.idevento = ingaggio.idevento) AND (i.idorganizzazione = ingaggio.idorganizzazione))))) atr
                              WHERE (atr.id = ANY (array_agg(ingaggio.idattrezzatura))))) AS attrezzature
                       FROM utl_ingaggio ingaggio
                      WHERE (ingaggio.idorganizzazione IS NOT NULL)
                      GROUP BY ingaggio.idorganizzazione, ingaggio.idevento;")->execute();



        Yii::$app->db->createCommand("CREATE VIEW view_report_attivazioni_volontari AS 
        SELECT i.id AS id_attivazione,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            vol.datore_di_lavoro,
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
           FROM (((((((((((((((((((((con_volontario_ingaggio cv
             LEFT JOIN utl_ingaggio i ON ((i.id = cv.id_ingaggio)))
             LEFT JOIN vol_volontario vol ON ((vol.id = cv.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = vol.id_anagrafica)))
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
          WHERE (i.idevento IS NOT NULL)
          GROUP BY cv.id, ana.id, vol.id, i.id, e.id, t.id, st.id, g.id, c.id, p.id, a.id, ta.id, attr.id, attrta.id, v.id, s.id, cs.id, ps.id;")->execute();

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
          WHERE (i.idevento IS NOT NULL)
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
          WHERE ((( SELECT count(u.id) AS count
                   FROM utl_ingaggio u
                  WHERE (u.idevento = e.id)) = 0) AND (e.id IS NOT NULL));")->execute();


        Yii::$app->db->createCommand("CREATE VIEW view_volontari_attivazioni AS 
         SELECT concat(i.id, '_', v.id) AS id,
            o.ref_id AS num_elenco_territoriale,
            o.denominazione,
            e.num_protocollo AS protocollo_evento,
            NULL::character varying AS protocollo_fronte,
            e.id AS id_evento,
            NULL::integer AS id_fronte,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS localita,
            c.comune,
            p.sigla AS provincia,
            tp.id AS id_tipologia,
            tp.tipologia,
            stp.id AS id_sottotipologia,
            stp.tipologia AS sottotipologia,
            a.id AS id_mezzo,
            ta.descrizione AS mezzo,
            a.targa,
            att.id AS id_attrezzatura,
            tatt.descrizione AS attrezzatura,
            att.modello,
                CASE
                    WHEN (a.id IS NOT NULL) THEN concat(ta.descrizione, ', targa: ', a.targa)
                    WHEN (att.id IS NOT NULL) THEN concat(tatt.descrizione, ', modello: ', att.modello)
                    ELSE ''::text
                END AS full_mezzo,
            i.id AS id_attivazione,
            i.created_at AS creazione,
            i.closed_at AS chiusura,
            i.stato,
            v.id AS id_volontario,
            ana.id AS id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
           FROM ((((((((((((((utl_evento e
             LEFT JOIN utl_evento eg ON ((eg.id = e.idparent)))
             LEFT JOIN utl_tipologia tp ON ((tp.id = e.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_tipologia stp ON ((stp.id = e.sottotipologia_evento)))
             LEFT JOIN utl_ingaggio i ON ((i.idevento = e.id)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura att ON ((att.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo tatt ON ((tatt.id = att.idtipo)))
             LEFT JOIN con_volontario_ingaggio ci ON ((ci.id_ingaggio = i.id)))
             LEFT JOIN vol_volontario v ON ((v.id = ci.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione o ON ((o.id = i.idorganizzazione)))
          WHERE ((ana.id IS NOT NULL) AND (e.idparent IS NULL))
        UNION ALL
         SELECT concat(i.id, '_', v.id) AS id,
            o.ref_id AS num_elenco_territoriale,
            o.denominazione,
            eg.num_protocollo AS protocollo_evento,
            e.num_protocollo AS protocollo_fronte,
            eg.id AS id_evento,
            e.id AS id_fronte,
                CASE
                    WHEN ((e.luogo IS NOT NULL) AND ((e.luogo)::text <> ''::text)) THEN e.luogo
                    ELSE e.indirizzo
                END AS localita,
            c.comune,
            p.sigla AS provincia,
            tp.id AS id_tipologia,
            tp.tipologia,
            stp.id AS id_sottotipologia,
            stp.tipologia AS sottotipologia,
            a.id AS id_mezzo,
            ta.descrizione AS mezzo,
            a.targa,
            att.id AS id_attrezzatura,
            tatt.descrizione AS attrezzatura,
            att.modello,
                CASE
                    WHEN (a.id IS NOT NULL) THEN concat(ta.descrizione, ', targa: ', a.targa)
                    WHEN (att.id IS NOT NULL) THEN concat(tatt.descrizione, ', modello: ', att.modello)
                    ELSE ''::text
                END AS full_mezzo,
            i.id AS id_attivazione,
            i.created_at AS creazione,
            i.closed_at AS chiusura,
            i.stato,
            v.id AS id_volontario,
            ana.id AS id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
           FROM ((((((((((((((utl_evento e
             LEFT JOIN utl_evento eg ON ((eg.id = e.idparent)))
             LEFT JOIN utl_tipologia tp ON ((tp.id = e.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = e.idcomune)))
             LEFT JOIN loc_provincia p ON ((p.id = c.id_provincia)))
             LEFT JOIN utl_tipologia stp ON ((stp.id = e.sottotipologia_evento)))
             LEFT JOIN utl_ingaggio i ON ((i.idevento = e.id)))
             LEFT JOIN utl_automezzo a ON ((a.id = i.idautomezzo)))
             LEFT JOIN utl_automezzo_tipo ta ON ((ta.id = a.idtipo)))
             LEFT JOIN utl_attrezzatura att ON ((att.id = i.idattrezzatura)))
             LEFT JOIN utl_attrezzatura_tipo tatt ON ((tatt.id = att.idtipo)))
             LEFT JOIN con_volontario_ingaggio ci ON ((ci.id_ingaggio = i.id)))
             LEFT JOIN vol_volontario v ON ((v.id = ci.id_volontario)))
             LEFT JOIN utl_anagrafica ana ON ((ana.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione o ON ((o.id = i.idorganizzazione)))
          WHERE ((ana.id IS NOT NULL) AND (e.idparent IS NOT NULL));")->execute();


        Yii::$app->db->createCommand("CREATE VIEW view_organizzazioni AS 
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            utl_automezzo.meta AS ref_meta,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            utl_automezzo_tipo.id AS tipo_automezzo_id,
            utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM (((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (((utl_automezzo.disponibilita)::text = '1'::text) AND (vol_organizzazione.stato_iscrizione = 3))
          GROUP BY utl_automezzo.id, utl_automezzo_tipo.id, vol_sede.id, vol_organizzazione.id, utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            utl_attrezzatura.meta AS ref_meta,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE ((utl_attrezzatura.idautomezzo IS NULL) AND (utl_attrezzatura.disponibilita = 1) AND (vol_organizzazione.stato_iscrizione = 3))
          GROUP BY utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_sede.id, vol_organizzazione.id, vol_organizzazione.ref_id
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            NULL::jsonb AS ref_meta,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.ref_id AS codice_associazione,
            vol_organizzazione.num_comunale,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            NULL::integer AS tipo_automezzo_id,
            ''::character varying AS tipo_automezzo_descrizione,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo,
            vol_organizzazione.pec_responsabile,
            vol_organizzazione.tel_responsabile,
            vol_organizzazione.email_responsabile,
            vol_organizzazione.nome_responsabile,
            vol_organizzazione.ambito
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3)
          GROUP BY vol_sede.id, vol_organizzazione.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id;")->execute();


        Yii::$app->db->createCommand("CREATE VIEW geo_datas AS 
         SELECT utl_automezzo.id AS ref_id,
            utl_automezzo.targa AS ref_identifier,
            'automezzo'::text AS tipologia_risorsa,
            utl_automezzo.engaged AS ref_engaged,
            utl_automezzo_tipo.id AS ref_tipo_id,
            utl_automezzo_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            utl_automezzo.id AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            utl_automezzo_tipo.is_mezzo_aereo
           FROM ((((((((utl_automezzo
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_automezzo.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_automezzo_tipo ON ((utl_automezzo_tipo.id = utl_automezzo.idtipo)))
             LEFT JOIN utl_attrezzatura ON ((utl_attrezzatura.idautomezzo = utl_automezzo.id)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
        UNION
         SELECT utl_attrezzatura.id AS ref_id,
            utl_attrezzatura.modello AS ref_identifier,
            'attrezzatura'::text AS tipologia_risorsa,
            utl_attrezzatura.engaged AS ref_engaged,
            utl_attrezzatura_tipo.id AS ref_tipo_id,
            utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            utl_attrezzatura_tipo.id AS tipo_attrezzatura_id,
            utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione,
            utl_attrezzatura.id AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo
           FROM ((((((utl_attrezzatura
             LEFT JOIN vol_sede ON ((vol_sede.id = utl_attrezzatura.idsede)))
             LEFT JOIN utl_specializzazione ON ((utl_specializzazione.id = vol_sede.id_specializzazione)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN utl_attrezzatura_tipo ON ((utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (utl_attrezzatura.idautomezzo IS NULL)
        UNION
         SELECT vol_sede.id AS ref_id,
            vol_sede.indirizzo AS ref_identifier,
            'sede'::text AS tipologia_risorsa,
            false AS ref_engaged,
            NULL::integer AS ref_tipo_id,
            ''::character varying AS ref_tipo_descrizione,
            vol_sede.id AS id_sede,
            vol_sede.indirizzo AS indirizzo_sede,
            vol_sede.telefono AS telefono_sede,
            vol_sede.altro_telefono AS altro_telefono_sede,
            vol_sede.fax AS fax_sede,
            vol_sede.disponibilita_oraria AS disponibilita_oraria_sede,
            vol_sede.cellulare AS cellulare_sede,
            vol_sede.geom AS geom_sede,
            vol_sede.lat,
            vol_sede.lon,
            vol_sede.tipo AS tipo_sede,
            utl_specializzazione.descrizione AS specializzazione_sede,
            utl_specializzazione.id AS id_specializzazione_sede,
            vol_organizzazione.id AS id_organizzazione,
            vol_organizzazione.denominazione AS denominazione_organizzazione,
            NULL::integer AS tipo_attrezzatura_id,
            ''::character varying AS tipo_attrezzatura_descrizione,
            NULL::integer AS id_attrezzatura,
            NULL::integer AS id_automezzo,
            loc_comune.id AS id_comune,
            loc_comune.comune,
            loc_provincia.id AS id_provincia,
            loc_provincia.provincia,
            false AS is_mezzo_aereo
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)));")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200819_110548_alter_data_length cannot be reverted.\n";

        return false;
    }
    */
}
