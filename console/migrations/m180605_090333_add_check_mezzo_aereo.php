<?php

use yii\db\Migration;

/**
 * Class m180605_090333_add_check_mezzo_aereo
 */
class m180605_090333_add_check_mezzo_aereo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        

        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS routing.m_view_organizzazioni")
            ->execute();
        
        Yii::$app->db->createCommand("DROP VIEW  IF EXISTS geo_datas")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW  IF EXISTS view_organizzazioni")
            ->execute();
        
        Yii::$app->db->createCommand("DROP VIEW  IF EXISTS routing.view_ingaggio_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW  IF EXISTS routing.view_organizzazioni")
            ->execute();

        $this->dropForeignKey(
            'fk_utl_automezzo_categoria',
            'utl_automezzo'
        );

        $this->addColumn( 'utl_automezzo_tipo', 'is_mezzo_aereo', $this->boolean()->defaultValue(false) );
        $this->dropColumn( 'utl_automezzo', 'idcategoria' );

        

        Yii::$app->db->createCommand("CREATE VIEW geo_datas AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo,
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
                NULL AS id_automezzo,
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
                NULL AS id_automezzo,
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


        Yii::$app->db->createCommand("CREATE VIEW view_organizzazioni AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo,
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
                NULL AS id_automezzo,                
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
                NULL AS id_automezzo,                
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
                utl_automezzo.id AS id_automezzo,
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
                NULL AS id_automezzo,                
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
                NULL AS id_automezzo,                
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


        Yii::$app->db->createCommand("CREATE VIEW routing.view_ingaggio_organizzazioni AS SELECT ingaggio.idevento,
                        ingaggio.idorganizzazione,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(aut.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE(aut.ingaggio_stato::text, ''::text), 'ingaggio_created_at', COALESCE(aut.ingaggio_created_at::text, ''::text), 'ingaggio_updated_at', COALESCE(aut.ingaggio_updated_at::text, ''::text), 'ingaggio_closed_at', COALESCE(aut.ingaggio_closed_at::text, ''::text), 'id', COALESCE(aut.id::text, ''::text), 'targa', COALESCE(aut.targa::text, ''::text), 'data_immatricolazione', COALESCE(aut.data_immatricolazione::text, ''::text), 'idsquadra', COALESCE(aut.idsquadra::text, ''::text), 'classe', COALESCE(aut.classe::text, ''::text), 'sottoclasse', COALESCE(aut.sottoclasse::text, ''::text), 'modello', COALESCE(aut.modello::text, ''::text), 'idtipo', COALESCE(aut.idtipo::text, ''::text), 'capacita', COALESCE(aut.capacita::text, ''::text), 'disponibilita', COALESCE(aut.disponibilita::text, ''::text), 'idorganizzazione', COALESCE(aut.idorganizzazione::text, ''::text), 'idsede', COALESCE(aut.idsede::text, ''::text), 'tempo_attivazione', COALESCE(aut.tempo_attivazione::text, ''::text), 'allestimento', COALESCE(aut.allestimento::text, ''::text), 'engaged', COALESCE(aut.engaged::text, ''::text)) AS automezzo
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
                                       FROM utl_automezzo a
                                         JOIN utl_ingaggio i ON i.idautomezzo = a.id AND i.idevento = ingaggio.idevento AND i.idorganizzazione = ingaggio.idorganizzazione) aut
                              WHERE aut.id = ANY (array_agg(ingaggio.idautomezzo)))) AS automezzi,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(atr.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE(atr.ingaggio_stato::text, ''::text), 'ingaggio_created_at', COALESCE(atr.ingaggio_created_at::text, ''::text), 'ingaggio_updated_at', COALESCE(atr.ingaggio_updated_at::text, ''::text), 'ingaggio_closed_at', COALESCE(atr.ingaggio_closed_at::text, ''::text), 'id', COALESCE(atr.id::text, ''::text), 'idtipo', COALESCE(atr.idtipo::text, ''::text), 'classe', COALESCE(atr.classe::text, ''::text), 'sottoclasse', COALESCE(atr.sottoclasse::text, ''::text), 'modello', COALESCE(atr.modello::text, ''::text), 'capacita', COALESCE(atr.capacita::text, ''::text), 'unita', COALESCE(atr.unita::text, ''::text), 'idorganizzazione', COALESCE(atr.idorganizzazione::text, ''::text), 'idsede', COALESCE(atr.idsede::text, ''::text), 'idautomezzo', COALESCE(atr.idautomezzo::text, ''::text), 'tempo_attivazione', COALESCE(atr.tempo_attivazione::text, ''::text), 'allestimento', COALESCE(atr.allestimento::text, ''::text), 'engaged', COALESCE(atr.engaged::text, ''::text)) AS attrezzatura
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
                                       FROM utl_attrezzatura a
                                         JOIN utl_ingaggio i ON i.idattrezzatura = a.id AND i.idevento = ingaggio.idevento AND i.idorganizzazione = ingaggio.idorganizzazione) atr
                              WHERE atr.id = ANY (array_agg(ingaggio.idattrezzatura)))) AS attrezzature
                       FROM utl_ingaggio ingaggio
                      WHERE ingaggio.idorganizzazione IS NOT NULL
                      GROUP BY ingaggio.idorganizzazione, ingaggio.idevento;")
                    ->execute();




    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW routing.m_view_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW routing.view_ingaggio_organizzazioni")
            ->execute();

        Yii::$app->db->createCommand("DROP VIEW routing.view_organizzazioni")
            ->execute();
        
        Yii::$app->db->createCommand("DROP VIEW geo_datas")
            ->execute();


        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni")
            ->execute();

        $this->dropColumn('utl_automezzo_tipo', 'is_mezzo_aereo');

        $this->addColumn('utl_automezzo', 'idcategoria', $this->integer());
        $this->addForeignKey(
            'fk_utl_automezzo_categoria',
            'utl_automezzo',
            'idcategoria',
            'utl_categoria_automezzo_attrezzatura',
            'id',
            'SET NULL'
        );

        

        Yii::$app->db->createCommand("CREATE VIEW geo_datas AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN utl_categoria_automezzo_attrezzatura ON utl_categoria_automezzo_attrezzatura.id = utl_automezzo.idcategoria
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
                NULL AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN utl_categoria_automezzo_attrezzatura ON utl_categoria_automezzo_attrezzatura.id = utl_attrezzatura.idcategoria
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
                NULL AS id_automezzo,
                NULL AS id_categoria, '' AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede 
                LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();


        Yii::$app->db->createCommand("CREATE VIEW view_organizzazioni AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, vol_sede.tipo AS tipo_sede, utl_specializzazione.descrizione as specializzazione_sede, utl_specializzazione.id as id_specializzazione_sede,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN utl_categoria_automezzo_attrezzatura ON utl_categoria_automezzo_attrezzatura.id = utl_automezzo.idcategoria
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
                NULL AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN utl_categoria_automezzo_attrezzatura ON utl_categoria_automezzo_attrezzatura.id = utl_attrezzatura.idcategoria
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
                NULL AS id_automezzo,
                NULL AS id_categoria, '' AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede 
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
                utl_automezzo.id AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
                LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idautomezzo = utl_automezzo.id
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN utl_categoria_automezzo_attrezzatura ON utl_categoria_automezzo_attrezzatura.id = utl_automezzo.idcategoria
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
                NULL AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede
                LEFT JOIN utl_specializzazione ON utl_specializzazione.id = vol_sede.id_specializzazione
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
                LEFT JOIN utl_categoria_automezzo_attrezzatura ON utl_categoria_automezzo_attrezzatura.id = utl_attrezzatura.idcategoria
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
                NULL AS id_automezzo,
                NULL AS id_categoria, '' AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede 
                LEFT JOIN utl_specializzazione ON vol_sede.id_specializzazione = utl_specializzazione.id
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW routing.m_view_organizzazioni AS SELECT * FROM view_organizzazioni;")
                    ->execute();

        Yii::$app->db->createCommand("CREATE VIEW routing.view_ingaggio_organizzazioni AS SELECT ingaggio.idevento,
                        ingaggio.idorganizzazione,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(aut.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE(aut.ingaggio_stato::text, ''::text), 'ingaggio_created_at', COALESCE(aut.ingaggio_created_at::text, ''::text), 'ingaggio_updated_at', COALESCE(aut.ingaggio_updated_at::text, ''::text), 'ingaggio_closed_at', COALESCE(aut.ingaggio_closed_at::text, ''::text), 'id', COALESCE(aut.id::text, ''::text), 'targa', COALESCE(aut.targa::text, ''::text), 'data_immatricolazione', COALESCE(aut.data_immatricolazione::text, ''::text), 'idsquadra', COALESCE(aut.idsquadra::text, ''::text), 'classe', COALESCE(aut.classe::text, ''::text), 'sottoclasse', COALESCE(aut.sottoclasse::text, ''::text), 'modello', COALESCE(aut.modello::text, ''::text), 'idcategoria', COALESCE(aut.idcategoria::text, ''::text), 'idtipo', COALESCE(aut.idtipo::text, ''::text), 'capacita', COALESCE(aut.capacita::text, ''::text), 'disponibilita', COALESCE(aut.disponibilita::text, ''::text), 'idorganizzazione', COALESCE(aut.idorganizzazione::text, ''::text), 'idsede', COALESCE(aut.idsede::text, ''::text), 'tempo_attivazione', COALESCE(aut.tempo_attivazione::text, ''::text), 'allestimento', COALESCE(aut.allestimento::text, ''::text), 'engaged', COALESCE(aut.engaged::text, ''::text)) AS automezzo
                               FROM ( SELECT a.id,
                                        a.targa,
                                        a.data_immatricolazione,
                                        a.idsquadra,
                                        a.classe,
                                        a.sottoclasse,
                                        a.modello,
                                        a.idcategoria,
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
                                       FROM utl_automezzo a
                                         JOIN utl_ingaggio i ON i.idautomezzo = a.id AND i.idevento = ingaggio.idevento AND i.idorganizzazione = ingaggio.idorganizzazione) aut
                              WHERE aut.id = ANY (array_agg(ingaggio.idautomezzo)))) AS automezzi,
                        array_to_json(ARRAY( SELECT json_build_object('ingaggio_note', COALESCE(atr.ingaggio_note, ''::text), 'ingaggio_stato', COALESCE(atr.ingaggio_stato::text, ''::text), 'ingaggio_created_at', COALESCE(atr.ingaggio_created_at::text, ''::text), 'ingaggio_updated_at', COALESCE(atr.ingaggio_updated_at::text, ''::text), 'ingaggio_closed_at', COALESCE(atr.ingaggio_closed_at::text, ''::text), 'id', COALESCE(atr.id::text, ''::text), 'idcategoria', COALESCE(atr.idcategoria::text, ''::text), 'idtipo', COALESCE(atr.idtipo::text, ''::text), 'classe', COALESCE(atr.classe::text, ''::text), 'sottoclasse', COALESCE(atr.sottoclasse::text, ''::text), 'modello', COALESCE(atr.modello::text, ''::text), 'capacita', COALESCE(atr.capacita::text, ''::text), 'unita', COALESCE(atr.unita::text, ''::text), 'idorganizzazione', COALESCE(atr.idorganizzazione::text, ''::text), 'idsede', COALESCE(atr.idsede::text, ''::text), 'idautomezzo', COALESCE(atr.idautomezzo::text, ''::text), 'tempo_attivazione', COALESCE(atr.tempo_attivazione::text, ''::text), 'allestimento', COALESCE(atr.allestimento::text, ''::text), 'engaged', COALESCE(atr.engaged::text, ''::text)) AS attrezzatura
                               FROM ( SELECT a.id,
                                        a.idcategoria,
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
                                       FROM utl_attrezzatura a
                                         JOIN utl_ingaggio i ON i.idattrezzatura = a.id AND i.idevento = ingaggio.idevento AND i.idorganizzazione = ingaggio.idorganizzazione) atr
                              WHERE atr.id = ANY (array_agg(ingaggio.idattrezzatura)))) AS attrezzature
                       FROM utl_ingaggio ingaggio
                      WHERE ingaggio.idorganizzazione IS NOT NULL
                      GROUP BY ingaggio.idorganizzazione, ingaggio.idevento;")
                    ->execute();

    }

}
