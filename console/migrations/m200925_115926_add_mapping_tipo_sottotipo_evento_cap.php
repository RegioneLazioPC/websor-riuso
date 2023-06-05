<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m200925_115926_add_mapping_tipo_sottotipo_evento_cap
 */
class m200925_115926_add_mapping_tipo_sottotipo_evento_cap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cap_mapping_profile_event_types', [
            'id' => $this->primaryKey(),
            'profilo' => $this->string(),
            'stringa_tipo_evento' => $this->string(1000),
            'id_tipo_evento' => $this->integer(),
            'id_sottotipo_evento' => $this->integer(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        Yii::$app->db->createCommand("CREATE UNIQUE INDEX idx_unique_cap_mapping_strings
            ON cap_mapping_profile_event_types(profilo, stringa_tipo_evento);")->execute();

        $this->addForeignKey(
            'fk-cap_profile_event_type',
            'cap_mapping_profile_event_types',
            'id_tipo_evento',
            'utl_tipologia', 
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk-cap_profile_event_subtype',
            'cap_mapping_profile_event_types',
            'id_sottotipo_evento',
            'utl_tipologia', 
            'id',
            'SET NULL'
        );

        /**
         * GESTIONE ENUM
         
        Yii::$app->db->createCommand("ALTER TYPE utl_segnalazione_fonte ADD VALUE 'Feed CAP';")->execute();
        */
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        /*
        GESTIONE ENUM
        Yii::$app->db->createCommand("ALTER TYPE utl_segnalazione_fonte RENAME TO utl_segnalazione_fonte_old;")->execute();

        Yii::$app->db->createCommand("CREATE TYPE utl_segnalazione_fonte AS ENUM('Telefono','Radio','Email','App');")->execute();

        Yii::$app->db->createCommand("DROP view view_segnalazioni;")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cartografia_segnalazioni;")->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_segnalazione ALTER COLUMN fonte TYPE utl_segnalazione_fonte USING fonte::text::utl_segnalazione_fonte;")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_segnalazioni AS 
            SELECT s.id,
                s.idutente,
                s.tipologia_evento AS id_tipologia,
                    CASE
                        WHEN (s.sos = false) THEN t.tipologia
                        ELSE 'Sos'::character varying
                    END AS tipologia,
                s.indirizzo,
                s.direzione,
                s.distanza,
                s.dataora_segnalazione,
                s.stato,
                s.fonte,
                s.num_protocollo,
                s.pericolo,
                s.feriti,
                s.vittime,
                s.interruzione_viabilita,
                s.aiuto_segnalatore,
                s.geom,
                s.note,
                s.lat,
                s.lon,
                s.idcomune AS id_comune,
                c.comune,
                m.orientation AS foto_orientation,
                m.id AS foto_id,
                    CASE
                        WHEN (m.nome IS NOT NULL) THEN concat('images/uploads/', m.ext, '/', m.date_upload, '/', m.nome)
                        ELSE NULL::text
                    END AS foto_url
               FROM ((((utl_segnalazione s
                 LEFT JOIN con_upl_media_utl_segnalazione conn ON ((conn.id_segnalazione = s.id)))
                 LEFT JOIN upl_media m ON ((m.id = conn.id_media)))
                 LEFT JOIN utl_tipologia t ON ((t.id = s.tipologia_evento)))
                 LEFT JOIN loc_comune c ON ((c.id = s.idcomune)));")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_segnalazioni AS 
            SELECT utl_segnalazione.id,
                utl_segnalazione.idutente,
                utl_segnalazione.idsalaoperativa,
                utl_segnalazione.foto,
                utl_segnalazione.tipologia_evento,
                utl_segnalazione.note,
                utl_segnalazione.lat,
                utl_segnalazione.lon,
                utl_segnalazione.idcomune,
                utl_segnalazione.indirizzo,
                utl_segnalazione.luogo,
                utl_segnalazione.direzione,
                utl_segnalazione.distanza,
                utl_segnalazione.dataora_segnalazione,
                utl_segnalazione.stato,
                utl_segnalazione.fonte,
                utl_segnalazione.num_protocollo,
                utl_segnalazione.foto_locale,
                utl_segnalazione.pericolo,
                utl_segnalazione.feriti,
                utl_segnalazione.vittime,
                utl_segnalazione.interruzione_viabilita,
                utl_segnalazione.aiuto_segnalatore,
                utl_segnalazione.geom,
                tipo.id AS id_tipo_segnalazione,
                tipo.tipologia AS tipologia_tipo_segnalazione,
                loc_comune.comune,
                utl_anagrafica.nome,
                utl_anagrafica.cognome,
                utl_anagrafica.codfiscale,
                utl_anagrafica.email,
                utl_anagrafica.matricola,
                upl_media.id AS media_id,
                upl_media.orientation AS media_orientation,
                upl_tipo_media.descrizione AS media_tipo
               FROM (((((((utl_segnalazione
                 LEFT JOIN utl_tipologia tipo ON ((tipo.id = utl_segnalazione.tipologia_evento)))
                 LEFT JOIN loc_comune ON ((loc_comune.id = utl_segnalazione.idcomune)))
                 LEFT JOIN utl_utente ON ((utl_utente.id = utl_segnalazione.idutente)))
                 LEFT JOIN utl_anagrafica ON ((utl_anagrafica.id = utl_utente.id_anagrafica)))
                 LEFT JOIN con_upl_media_utl_segnalazione ON ((con_upl_media_utl_segnalazione.id_segnalazione = utl_segnalazione.id)))
                 LEFT JOIN upl_media ON ((upl_media.id = con_upl_media_utl_segnalazione.id_media)))
                 LEFT JOIN upl_tipo_media ON (((upl_tipo_media.id = upl_media.id_tipo_media) AND ((upl_tipo_media.descrizione)::text = 'Immagine segnalazione'::text))));")->execute();
        */


        $this->dropForeignKey(
            'fk-cap_profile_event_type',
            'cap_mapping_profile_event_types'
        );
        $this->dropForeignKey(
            'fk-cap_profile_event_subtype',
            'cap_mapping_profile_event_types'
        );

        Yii::$app->db->createCommand("DROP INDEX idx_unique_cap_mapping_strings;")->execute();
        $this->dropTable("cap_mapping_profile_event_types");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_115926_add_mapping_tipo_sottotipo_evento_cap cannot be reverted.\n";

        return false;
    }
    */
}
