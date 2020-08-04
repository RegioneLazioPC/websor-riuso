<?php

use yii\db\Migration;

/**
 * Class m190616_164508_add_foto_id_to_view_cartografia
 */
class m190616_164508_add_foto_id_to_view_cartografia extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cartografia_segnalazioni")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_segnalazioni as
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
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cartografia_segnalazioni")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_segnalazioni as
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
                CASE
                    WHEN (m.nome IS NOT NULL) THEN concat('images/uploads/', m.ext, '/', m.date_upload, '/', m.nome)
                    ELSE NULL::text
                END AS foto_url
           FROM ((((utl_segnalazione s
             LEFT JOIN con_upl_media_utl_segnalazione conn ON ((conn.id_segnalazione = s.id)))
             LEFT JOIN upl_media m ON ((m.id = conn.id_media)))
             LEFT JOIN utl_tipologia t ON ((t.id = s.tipologia_evento)))
             LEFT JOIN loc_comune c ON ((c.id = s.idcomune)));")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190616_164508_add_foto_id_to_view_cartografia cannot be reverted.\n";

        return false;
    }
    */
}
