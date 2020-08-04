<?php

use yii\db\Migration;

/**
 * Class m200212_152608_alter_view_view_organizzazioni
 */
class m200212_152608_alter_view_view_organizzazioni extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")->execute();
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")->execute();
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
            vol_organizzazione.nome_responsabile
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
            vol_organizzazione.nome_responsabile
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
            vol_organizzazione.nome_responsabile
           FROM ((((vol_sede
             LEFT JOIN utl_specializzazione ON ((vol_sede.id_specializzazione = utl_specializzazione.id)))
             LEFT JOIN vol_organizzazione ON ((vol_organizzazione.id = vol_sede.id_organizzazione)))
             LEFT JOIN loc_comune ON ((loc_comune.id = vol_sede.comune)))
             LEFT JOIN loc_provincia ON ((loc_provincia.id = loc_comune.id_provincia)))
          WHERE (vol_organizzazione.stato_iscrizione = 3)
          GROUP BY vol_sede.id, vol_organizzazione.id, loc_comune.id, loc_provincia.id, vol_organizzazione.ref_id;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200212_152608_alter_view_view_organizzazioni cannot be reverted.\n";

        return false;
    }
    */
}
