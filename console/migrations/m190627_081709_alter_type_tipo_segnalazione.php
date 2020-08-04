<?php

use yii\db\Migration;

/**
 * Class m190627_081709_alter_type_tipo_segnalazione
 */
class m190627_081709_alter_type_tipo_segnalazione extends Migration
{
    /**
     * {@inheritdoc}
     * no transaction
     */
    public function up()
    {
        
        Yii::$app->db->createCommand("ALTER TYPE utl_segnalazione_stato ADD VALUE 'Nuova in lavorazione';")->execute();
        Yii::$app->db->createCommand("UPDATE utl_segnalazione SET stato = 'Nuova in lavorazione' WHERE stato = 'Nuova e assegnata al SOP';")->execute();
        Yii::$app->db->createCommand("ALTER TYPE utl_segnalazione_stato RENAME TO utl_segnalazione_stato_old;")->execute();
        Yii::$app->db->createCommand("CREATE TYPE utl_segnalazione_stato AS ENUM('Nuova in lavorazione','Verificata e trasformata in evento','Chiusa');")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cartografia_segnalazioni;")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_segnalazioni;")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_segnalazione ALTER COLUMN stato TYPE utl_segnalazione_stato USING stato::text::utl_segnalazione_stato;")->execute();
        Yii::$app->db->createCommand("DROP TYPE utl_segnalazione_stato_old;")->execute();


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
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        
        Yii::$app->db->createCommand("ALTER TYPE utl_segnalazione_stato ADD VALUE 'Nuova e assegnata al SOP';")->execute();
        Yii::$app->db->createCommand("UPDATE utl_segnalazione SET stato = 'Nuova e assegnata al SOP' WHERE stato = 'Nuova in lavorazione';")->execute();
        Yii::$app->db->createCommand("ALTER TYPE utl_segnalazione_stato RENAME TO utl_segnalazione_stato_old;")->execute();
        Yii::$app->db->createCommand("CREATE TYPE utl_segnalazione_stato AS ENUM('Nuova e assegnata al SOP','Verificata e trasformata in evento','Chiusa');")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cartografia_segnalazioni;")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_segnalazioni;")->execute();
        Yii::$app->db->createCommand("ALTER TABLE utl_segnalazione ALTER COLUMN stato TYPE utl_segnalazione_stato USING stato::text::utl_segnalazione_stato;")->execute();
        Yii::$app->db->createCommand("DROP TYPE utl_segnalazione_stato_old;")->execute();


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
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_081709_alter_type_tipo_segnalazione cannot be reverted.\n";

        return false;
    }
    */
}
