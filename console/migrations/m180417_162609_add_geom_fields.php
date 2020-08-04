<?php

use yii\db\Migration;

/**
 * Class m180417_162609_add_geom_fields
 */
class m180417_162609_add_geom_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TABLE vol_sede ADD COLUMN geom geometry(Point, 4326)")
            ->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_evento ADD COLUMN geom geometry(Point, 4326)")
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW geo_datas")
            ->execute();

        Yii::$app->db->createCommand("ALTER TABLE vol_sede DROP COLUMN geom")
            ->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_evento DROP COLUMN geom")
            ->execute();

        
    }
    
}
