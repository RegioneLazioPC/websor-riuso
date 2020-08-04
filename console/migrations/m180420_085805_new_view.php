<?php

use yii\db\Migration;

/**
 * Class m180420_085805_new_view
 */
class m180420_085805_new_view extends Migration
{
    //0918040695
    //287179#
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW geo_datas")
            ->execute();

        Yii::$app->db->createCommand("CREATE VIEW geo_datas AS 
            SELECT utl_automezzo.id AS ref_id, utl_automezzo.targa AS ref_identifier, 'automezzo' AS tipologia_risorsa, utl_automezzo.engaged AS ref_engaged,
                utl_automezzo_tipo.id AS ref_tipo_id, utl_automezzo_tipo.descrizione AS ref_tipo_descrizione, 
                vol_sede.id AS id_sede, vol_sede.indirizzo AS indirizzo_sede, vol_sede.telefono AS telefono_sede, vol_sede.altro_telefono AS altro_telefono_sede, vol_sede.fax AS fax_sede,
                vol_sede.disponibilita_oraria AS disponibilita_oraria_sede, vol_sede.cellulare AS cellulare_sede, vol_sede.geom AS geom_sede,
                vol_sede.lat, vol_sede.lon, 
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                utl_automezzo.id AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede AND vol_sede.tipo = 'Sede Operativa'
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
                vol_sede.lat, vol_sede.lon,
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                utl_attrezzatura_tipo.id AS tipo_attrezzatura_id, utl_attrezzatura_tipo.descrizione AS tipo_attrezzatura_descrizione, utl_attrezzatura.id AS id_attrezzatura,
                NULL AS id_automezzo,
                utl_categoria_automezzo_attrezzatura.id AS id_categoria, utl_categoria_automezzo_attrezzatura.descrizione AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_attrezzatura
                LEFT JOIN vol_sede ON vol_sede.id = utl_attrezzatura.idsede AND vol_sede.tipo = 'Sede Operativa'
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
                vol_sede.lat, vol_sede.lon, 
                vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione AS denominazione_organizzazione, 
                NULL AS tipo_attrezzatura_id, '' AS tipo_attrezzatura_descrizione, NULL AS id_attrezzatura,
                NULL AS id_automezzo,
                NULL AS id_categoria, '' AS descrizione_categoria,
                loc_comune.id AS id_comune, loc_comune.comune,
                loc_provincia.id AS id_provincia, loc_provincia.provincia
                FROM utl_automezzo 
                LEFT JOIN vol_sede ON vol_sede.id = utl_automezzo.idsede AND vol_sede.tipo = 'Sede Operativa'
                LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
                LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
                LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW geo_datas")
            ->execute();


        Yii::$app->db->createCommand("CREATE VIEW geo_datas AS 
            SELECT 
            vol_sede.id, vol_sede.geom, vol_sede.indirizzo, vol_sede.email, vol_sede.email_pec, vol_sede.telefono, vol_sede.cellulare, vol_sede.altro_telefono, vol_sede.fax, 
            vol_sede.disponibilita_oraria,
            vol_organizzazione.id AS id_organizzazione, vol_organizzazione.denominazione, vol_organizzazione.codicefiscale, vol_organizzazione.partita_iva,
            loc_comune.comune, loc_comune.id AS id_comune, loc_provincia.provincia, loc_provincia.sigla,
            utl_automezzo.targa, utl_automezzo_tipo.descrizione as automezzo_descrizione, utl_automezzo_tipo.id AS id_utl_automezzo_tipo,
            utl_attrezzatura.modello, utl_attrezzatura.capacita, utl_attrezzatura.unita, utl_attrezzatura_tipo.descrizione as descrizione_attrezzatura, utl_attrezzatura_tipo.id AS id_utl_attrezzatura_tipo
            FROM vol_sede 
            LEFT JOIN vol_organizzazione ON vol_organizzazione.id = vol_sede.id_organizzazione
            LEFT JOIN utl_automezzo ON utl_automezzo.idsede = vol_sede.id
            LEFT JOIN utl_automezzo_tipo ON utl_automezzo_tipo.id = utl_automezzo.idtipo
            LEFT JOIN utl_attrezzatura ON utl_attrezzatura.idsede = vol_sede.id
            LEFT JOIN utl_attrezzatura_tipo ON utl_attrezzatura_tipo.id = utl_attrezzatura.idtipo
            LEFT JOIN loc_comune ON loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia
        ")
        ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180420_085805_new_view cannot be reverted.\n";

        return false;
    }
    */
}
