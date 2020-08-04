<?php

use yii\db\Migration;

/**
 * Class m180608_165140_alter_views_another_time
 */
class m180608_165140_alter_views_another_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW routing.m_view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW routing.view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_organizzazioni AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo, utl_automezzo_tipo.id AS tipo_automezzo_id, utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                utl_automezzo_tipo.is_mezzo_aereo AS is_mezzo_aereo,
                vol_organizzazione.pec_responsabile, vol_organizzazione.tel_responsabile, vol_organizzazione.email_responsabile, vol_organizzazione.nome_responsabile
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                UNION
                SELECT utl_attrezzatura.id AS ref_id, utl_attrezzatura.modello AS ref_identifier, 'attrezzatura' AS tipologia_risorsa, utl_attrezzatura.engaged AS ref_engaged,
                utl_attrezzatura_tipo.id AS ref_tipo_id, utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo,
                vol_organizzazione.pec_responsabile, vol_organizzazione.tel_responsabile, vol_organizzazione.email_responsabile, vol_organizzazione.nome_responsabile
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                WHERE utl_attrezzatura.idautomezzo IS NULL
                UNION
                SELECT vol_sede.id AS ref_id, vol_sede.indirizzo AS ref_identifier, 'sede' AS tipologia_risorsa, FALSE AS ref_engaged,
                NULL AS ref_tipo_id, '' AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                NULL AS tipo_attrezzatura_id, '' AS tipo_attrezzatura_descrizione, NULL AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo,
                vol_organizzazione.pec_responsabile, vol_organizzazione.tel_responsabile, vol_organizzazione.email_responsabile, vol_organizzazione.nome_responsabile
                FROM vol_sede 
                LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();

        Yii::$app->db->createCommand("CREATE VIEW routing.view_organizzazioni AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo, utl_automezzo_tipo.id AS tipo_automezzo_id, utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                utl_automezzo_tipo.is_mezzo_aereo AS is_mezzo_aereo,
                vol_organizzazione.pec_responsabile, vol_organizzazione.tel_responsabile, vol_organizzazione.email_responsabile, vol_organizzazione.nome_responsabile
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                UNION
                SELECT utl_attrezzatura.id AS ref_id, utl_attrezzatura.modello AS ref_identifier, 'attrezzatura' AS tipologia_risorsa, utl_attrezzatura.engaged AS ref_engaged,
                utl_attrezzatura_tipo.id AS ref_tipo_id, utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo,
                vol_organizzazione.pec_responsabile, vol_organizzazione.tel_responsabile, vol_organizzazione.email_responsabile, vol_organizzazione.nome_responsabile
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                WHERE utl_attrezzatura.idautomezzo IS NULL
                UNION
                SELECT vol_sede.id AS ref_id, vol_sede.indirizzo AS ref_identifier, 'sede' AS tipologia_risorsa, FALSE AS ref_engaged,
                NULL AS ref_tipo_id, '' AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                NULL AS tipo_attrezzatura_id, '' AS tipo_attrezzatura_descrizione, NULL AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo,
                vol_organizzazione.pec_responsabile, vol_organizzazione.tel_responsabile, vol_organizzazione.email_responsabile, vol_organizzazione.nome_responsabile
                FROM vol_sede 
                LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW routing.m_view_organizzazioni AS SELECT * FROM view_organizzazioni;")
                    ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW routing.m_view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW routing.view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_organizzazioni AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo, utl_automezzo_tipo.id AS tipo_automezzo_id, utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                utl_automezzo_tipo.is_mezzo_aereo AS is_mezzo_aereo
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                UNION
                SELECT utl_attrezzatura.id AS ref_id, utl_attrezzatura.modello AS ref_identifier, 'attrezzatura' AS tipologia_risorsa, utl_attrezzatura.engaged AS ref_engaged,
                utl_attrezzatura_tipo.id AS ref_tipo_id, utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                WHERE utl_attrezzatura.idautomezzo IS NULL
                UNION
                SELECT vol_sede.id AS ref_id, vol_sede.indirizzo AS ref_identifier, 'sede' AS tipologia_risorsa, FALSE AS ref_engaged,
                NULL AS ref_tipo_id, '' AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                NULL AS tipo_attrezzatura_id, '' AS tipo_attrezzatura_descrizione, NULL AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo
                FROM vol_sede 
                LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();

        Yii::$app->db->createCommand("CREATE VIEW routing.view_organizzazioni AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo, utl_automezzo_tipo.id AS tipo_automezzo_id, utl_automezzo_tipo.descrizione AS tipo_automezzo_descrizione,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                utl_automezzo_tipo.is_mezzo_aereo AS is_mezzo_aereo
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                UNION
                SELECT utl_attrezzatura.id AS ref_id, utl_attrezzatura.modello AS ref_identifier, 'attrezzatura' AS tipologia_risorsa, utl_attrezzatura.engaged AS ref_engaged,
                utl_attrezzatura_tipo.id AS ref_tipo_id, utl_attrezzatura_tipo.descrizione AS ref_tipo_descrizione,
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
                WHERE utl_attrezzatura.idautomezzo IS NULL
                UNION
                SELECT vol_sede.id AS ref_id, vol_sede.indirizzo AS ref_identifier, 'sede' AS tipologia_risorsa, FALSE AS ref_engaged,
                NULL AS ref_tipo_id, '' AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                NULL AS tipo_attrezzatura_id, '' AS tipo_attrezzatura_descrizione, NULL AS id_attrezzatura,
                NULL AS id_automezzo, NULL AS tipo_automezzo_id, '' AS tipo_automezzo_descrizione,               
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia,
                FALSE AS is_mezzo_aereo
                FROM vol_sede 
                LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW routing.m_view_organizzazioni AS SELECT * FROM view_organizzazioni;")
                    ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180608_165140_alter_views_another_time cannot be reverted.\n";

        return false;
    }
    */
}
