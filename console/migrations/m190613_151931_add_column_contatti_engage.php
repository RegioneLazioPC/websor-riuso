<?php

use yii\db\Migration;

/**
 * Class m190613_151931_add_column_contatti_engage
 */
class m190613_151931_add_column_contatti_engage extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW view_organizzazioni as
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
            vol_organizzazione.nome_responsabile,
            string_agg(( concat( org_contatto.note, ' - ', org_contatto.contatto ) )::text, ', '::text) AS contatti_attivazioni
           FROM utl_automezzo
             LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
             LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
             LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
             LEFT JOIN con_organizzazione_contatto ON con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id AND con_organizzazione_contatto.use_type = 1
             LEFT JOIN utl_contatto org_contatto ON org_contatto.id = con_organizzazione_contatto.id_contatto
             LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
             LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
             LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
             LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
             LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
          WHERE utl_automezzo.disponibilita::text = '1'::text AND vol_organizzazione.stato_iscrizione = 3
          GROUP BY utl_automezzo.id, utl_automezzo_tipo.id, vol_sede.id, utl_specializzazione.descrizione, utl_specializzazione.id, vol_organizzazione.id, utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id
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
                vol_organizzazione.nome_responsabile,
                string_agg(( concat( org_contatto.note, ' - ', org_contatto.contatto ) )::text, ', '::text) AS contatti_attivazioni
               FROM utl_attrezzatura
                 LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                 LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                 LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                 LEFT JOIN con_organizzazione_contatto ON con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id AND con_organizzazione_contatto.use_type = 1
                 LEFT JOIN utl_contatto org_contatto ON org_contatto.id = con_organizzazione_contatto.id_contatto
                 LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                 LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                 LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
              WHERE utl_attrezzatura.idautomezzo IS NULL AND utl_attrezzatura.disponibilita = 1 AND vol_organizzazione.stato_iscrizione = 3
              GROUP BY  utl_attrezzatura_tipo.id, utl_attrezzatura.id, loc_comune.id, loc_provincia.id, vol_sede.id, utl_specializzazione.descrizione, utl_specializzazione.id, vol_organizzazione.id
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
                vol_organizzazione.nome_responsabile,
                string_agg(( concat( org_contatto.note, ' - ', org_contatto.contatto ) )::text, ', '::text) AS contatti_attivazioni
               FROM vol_sede
                 LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                 LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                 LEFT JOIN con_organizzazione_contatto ON con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id AND con_organizzazione_contatto.use_type = 1
                 LEFT JOIN utl_contatto org_contatto ON org_contatto.id = con_organizzazione_contatto.id_contatto
                 LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                 LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
              WHERE vol_organizzazione.stato_iscrizione = 3
              GROUP BY vol_sede.id, utl_specializzazione.descrizione, utl_specializzazione.id, vol_organizzazione.id, loc_comune.id, loc_provincia.id;")->execute();

        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS routing.m_view_organizzazioni")->execute();
        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW routing.m_view_organizzazioni as
                SELECT view_organizzazioni.ref_id,
                    view_organizzazioni.ref_identifier,
                    view_organizzazioni.tipologia_risorsa,
                    view_organizzazioni.ref_engaged,
                    view_organizzazioni.ref_tipo_id,
                    view_organizzazioni.ref_tipo_descrizione,
                    view_organizzazioni.id_sede,
                    view_organizzazioni.indirizzo_sede,
                    view_organizzazioni.telefono_sede,
                    view_organizzazioni.altro_telefono_sede,
                    view_organizzazioni.fax_sede,
                    view_organizzazioni.disponibilita_oraria_sede,
                    view_organizzazioni.cellulare_sede,
                    view_organizzazioni.geom_sede,
                    view_organizzazioni.lat,
                    view_organizzazioni.lon,
                    view_organizzazioni.tipo_sede,
                    view_organizzazioni.specializzazione_sede,
                    view_organizzazioni.id_specializzazione_sede,
                    view_organizzazioni.id_organizzazione,
                    view_organizzazioni.denominazione_organizzazione,
                    view_organizzazioni.tipo_attrezzatura_id,
                    view_organizzazioni.tipo_attrezzatura_descrizione,
                    view_organizzazioni.id_attrezzatura,
                    view_organizzazioni.id_automezzo,
                    view_organizzazioni.tipo_automezzo_id,
                    view_organizzazioni.tipo_automezzo_descrizione,
                    view_organizzazioni.id_comune,
                    view_organizzazioni.comune,
                    view_organizzazioni.id_provincia,
                    view_organizzazioni.provincia,
                    view_organizzazioni.is_mezzo_aereo,
                    view_organizzazioni.pec_responsabile,
                    view_organizzazioni.tel_responsabile,
                    view_organizzazioni.email_responsabile,
                    view_organizzazioni.nome_responsabile
                   FROM view_organizzazioni;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP materialized VIEW IF EXISTS routing.m_view_organizzazioni;")
        ->execute();

        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni;")->execute();
        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW view_organizzazioni as
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
              WHERE (((utl_automezzo.disponibilita)::text = '1'::text) AND (vol_organizzazione.stato_iscrizione = 3))
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

          Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW routing.m_view_organizzazioni as
                SELECT view_organizzazioni.ref_id,
                    view_organizzazioni.ref_identifier,
                    view_organizzazioni.tipologia_risorsa,
                    view_organizzazioni.ref_engaged,
                    view_organizzazioni.ref_tipo_id,
                    view_organizzazioni.ref_tipo_descrizione,
                    view_organizzazioni.id_sede,
                    view_organizzazioni.indirizzo_sede,
                    view_organizzazioni.telefono_sede,
                    view_organizzazioni.altro_telefono_sede,
                    view_organizzazioni.fax_sede,
                    view_organizzazioni.disponibilita_oraria_sede,
                    view_organizzazioni.cellulare_sede,
                    view_organizzazioni.geom_sede,
                    view_organizzazioni.lat,
                    view_organizzazioni.lon,
                    view_organizzazioni.tipo_sede,
                    view_organizzazioni.specializzazione_sede,
                    view_organizzazioni.id_specializzazione_sede,
                    view_organizzazioni.id_organizzazione,
                    view_organizzazioni.denominazione_organizzazione,
                    view_organizzazioni.tipo_attrezzatura_id,
                    view_organizzazioni.tipo_attrezzatura_descrizione,
                    view_organizzazioni.id_attrezzatura,
                    view_organizzazioni.id_automezzo,
                    view_organizzazioni.tipo_automezzo_id,
                    view_organizzazioni.tipo_automezzo_descrizione,
                    view_organizzazioni.id_comune,
                    view_organizzazioni.comune,
                    view_organizzazioni.id_provincia,
                    view_organizzazioni.provincia,
                    view_organizzazioni.is_mezzo_aereo,
                    view_organizzazioni.pec_responsabile,
                    view_organizzazioni.tel_responsabile,
                    view_organizzazioni.email_responsabile,
                    view_organizzazioni.nome_responsabile
                   FROM view_organizzazioni;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190613_151931_add_column_contatti_engage cannot be reverted.\n";

        return false;
    }
    */
}
