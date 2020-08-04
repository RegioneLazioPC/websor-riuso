<?php

use yii\db\Migration;

/**
 * Class m190205_164428_alter_view_segnalazioni
 */
class m190205_164428_alter_view_segnalazioni extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_segnalazioni")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_segnalazioni as 
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
            upl_media.id as media_id,
            upl_media.orientation as media_orientation,
            upl_tipo_media.descrizione as media_tipo
           FROM utl_segnalazione
             LEFT JOIN utl_tipologia tipo ON tipo.id = utl_segnalazione.tipologia_evento
             LEFT JOIN loc_comune ON loc_comune.id = utl_segnalazione.idcomune
             LEFT JOIN utl_utente ON utl_utente.id = utl_segnalazione.idutente
             LEFT JOIN utl_anagrafica ON utl_anagrafica.id = utl_utente.id_anagrafica
             LEFT JOIN con_upl_media_utl_segnalazione ON con_upl_media_utl_segnalazione.id_segnalazione = utl_segnalazione.id
             LEFT JOIN upl_media ON upl_media.id = con_upl_media_utl_segnalazione.id_media
             LEFT JOIN upl_tipo_media ON upl_tipo_media.id = upl_media.id_tipo_media AND upl_tipo_media.descrizione = 'Immagine segnalazione'
             ;"
         )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_segnalazioni")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_segnalazioni as 
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
            utl_anagrafica.matricola
           FROM utl_segnalazione
             LEFT JOIN utl_tipologia tipo ON tipo.id = utl_segnalazione.tipologia_evento
             LEFT JOIN loc_comune ON loc_comune.id = utl_segnalazione.idcomune
             LEFT JOIN utl_utente ON utl_utente.id = utl_segnalazione.idutente
             LEFT JOIN utl_anagrafica ON utl_anagrafica.id = utl_utente.id_anagrafica;"
         )->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190205_164428_alter_view_segnalazioni cannot be reverted.\n";

        return false;
    }
    */
}
