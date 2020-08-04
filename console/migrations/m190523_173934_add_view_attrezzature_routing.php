<?php

use yii\db\Migration;

/**
 * Class m190523_173934_add_view_attrezzature_routing
 */
class m190523_173934_add_view_attrezzature_routing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_automezzi AS
            WITH query AS (
             SELECT utl_automezzo.idtipo,
                        utl_automezzo.idsede,
                        count(*) AS cnt
                       FROM utl_automezzo
                      GROUP BY utl_automezzo.idtipo, utl_automezzo.idsede
                    )
             SELECT row_number() OVER (ORDER BY q.cnt DESC) AS fid,
                q.cnt,
                q.idtipo AS tipo_mezzo,
                at.descrizione AS descrizione_mezzo,
                q.idsede,
                s.id_organizzazione,
                o.denominazione AS denominazione_org,
                CONCAT(s.indirizzo,', ',c.comune,' (',p.sigla,')') AS indirizzo,
                s.geom
               FROM query q,
                utl_automezzo_tipo at,
                vol_sede s,
                loc_comune c,
                loc_provincia p,
                vol_organizzazione o
              WHERE ((q.idtipo = at.id) AND (q.idsede = s.id) AND (s.id_organizzazione = o.id) AND (s.comune = c.id) AND (c.id_provincia = p.id));
            ")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_attrezzature AS
            WITH query AS (
                     SELECT utl_attrezzatura.idtipo,
                        utl_attrezzatura.idsede,
                        count(*) AS cnt
                       FROM utl_attrezzatura
                      GROUP BY utl_attrezzatura.idtipo, utl_attrezzatura.idsede
                    )
             SELECT row_number() OVER (ORDER BY q.cnt DESC) AS fid,
                q.cnt,
                q.idtipo AS tipo_attrezzatura,
                at.descrizione AS descrizione,
                q.idsede,
                s.id_organizzazione,
                o.denominazione AS denominazione_org,
                CONCAT(s.indirizzo,', ',c.comune,' (',p.sigla,')') AS indirizzo,
                s.geom
               FROM query q,
                utl_attrezzatura_tipo at,
                vol_sede s,
                loc_comune c,
                loc_provincia p,
                vol_organizzazione o
              WHERE ((q.idtipo = at.id) AND (q.idsede = s.id) AND (s.id_organizzazione = o.id) AND (s.comune = c.id) AND (c.id_provincia = p.id));
            ")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_segnalazioni AS
            SELECT 
              s.id,
              s.idutente,
              s.tipologia_evento as id_tipologia,
              CASE WHEN s.sos = false
              THEN t.tipologia
              ELSE 'Sos'
            END as tipologia,
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
              s.idcomune as id_comune,
              c.comune as comune,
              m.orientation as foto_orientation,
            CASE WHEN m.nome is not null
              THEN CONCAT('images/uploads/', m.ext, '/', m.date_upload, '/', m.nome)
              ELSE null
            END as foto_url
            FROM 
                utl_segnalazione s
            LEFT JOIN con_upl_media_utl_segnalazione conn ON conn.id_segnalazione = s.id
            LEFT JOIN upl_media m ON m.id = conn.id_media
            LEFT JOIN utl_tipologia t ON t.id = s.tipologia_evento
            LEFT JOIN loc_comune c ON c.id = s.idcomune;")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_eventi AS
            SELECT 
              e.id,
              e.tipologia_evento as id_tipologia,
              e.sottotipologia_evento as id_sottotipologia,
              CASE WHEN t.id is not null
              THEN t.tipologia
              ELSE 'Sos'
            END as tipologia,
              CASE WHEN st.id is not null
              THEN st.tipologia
              ELSE ''
            END as sottotipologia,
            CASE WHEN e.idparent is not null
              THEN 'Fronte'
              ELSE 'Evento'
            END as tipo,
              e.idparent as parent,
              e.num_protocollo,
              p.num_protocollo as protocollo_evento_genitore,
              e.indirizzo,   
              e.direzione,
              e.distanza,
              e.dataora_evento,
              e.stato,
              e.pericolo,
              e.feriti,
              e.vittime,
              e.note,
              e.interruzione_viabilita,
              e.aiuto_segnalatore,
              e.geom,
              e.lat,
              e.lon,
              e.is_public as pubblico,
              e.has_coc as coc,
              g.descrizione as gestore,
              sts.descrizione as sottostato,
              e.idcomune as id_comune,
              c.comune as comune              
            FROM 
                utl_evento e
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN utl_evento p ON p.id = e.idparent
            LEFT JOIN evt_gestore_evento g ON g.id = e.id_gestore_evento
            LEFT JOIN evt_sottostato_evento sts ON sts.id = e.id_sottostato_evento;")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_cartografia_organizzazioni AS
            SELECT 
                o.id,
                o.ref_id AS num_elenco_territoriale,
                o.denominazione,
                vt.tipologia,
                s.indirizzo,
                c.comune,
                o.codicefiscale,
                o.partita_iva,
                o.nome_responsabile,
                o.cf_rappresentante_legale,
                o.tel_responsabile,
                o.nome_referente,
                o.cf_referente,
                o.tel_referente,
                o.fax_referente,
                o.email_referente,
                o.data_costituzione,
                s.geom,
                s.lat,
                s.lon,
                s.tipo as tipo_sede,
                s.comune as id_comune
                FROM vol_sede s
                LEFT JOIN vol_organizzazione o ON o.id = s.id_organizzazione
                LEFT JOIN vol_tipo_organizzazione vt ON vt.id = o.id_tipo_organizzazione
                LEFT JOIN loc_comune c ON c.id = s.comune
                WHERE o.stato_iscrizione = 3")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cartografia_automezzi;
            ")->execute();

        Yii::$app->db->createCommand("DROP VIEW view_cartografia_attrezzature;
            ")->execute();

        Yii::$app->db->createCommand("DROP VIEW view_cartografia_segnalazioni;")->execute();

        Yii::$app->db->createCommand("DROP VIEW view_cartografia_eventi;")->execute();

        Yii::$app->db->createCommand("DROP VIEW view_cartografia_organizzazioni;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190523_173934_add_view_attrezzature_routing cannot be reverted.\n";

        return false;
    }
    */
}
