<?php

use yii\db\Migration;

/**
 * Class m200225_095729_create_view_for_report_attivazioni
 */
class m200225_095729_create_view_for_report_attivazioni extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_report_attivazioni AS 
            SELECT 
            i.id as id_attivazione,
            e.id as id_evento,
            i.created_at,
            i.closed_at,
            DATE_PART('day', i.closed_at::timestamp - i.created_at::timestamp) * 24 + 
            DATE_PART('hour', i.closed_at::timestamp - i.created_at::timestamp) * 60 +
            DATE_PART('minute', i.closed_at::timestamp - i.created_at::timestamp) as durata,
            lpad( DATE_PART('month', i.created_at::timestamp)::text, 2, '0') as mese,
            DATE_PART('month', i.created_at::timestamp) as mese_int,
            DATE_PART('year', i.created_at::timestamp) as anno,
            e.num_protocollo,
            CASE WHEN t.tipologia is not null THEN t.tipologia
                 ELSE 'Sos'
            END as tipologia,
            st.tipologia as sottotipologia,
            g.id as id_gestore,
            g.descrizione as gestore,
            CASE WHEN e.has_coc = 1 THEN 'Si'
                 ELSE 'No'
            END as coc,
            CASE WHEN e.luogo is not null AND e.luogo != '' THEN e.luogo
                 ELSE e.indirizzo
            END as indirizzo,
            c.id as id_comune,
            c.comune as comune,
            p.id as id_provincia,
            p.provincia as provincia,
            p.sigla as provincia_sigla,
            a.id as id_automezzo,
            a.targa as targa,
            ta.id as id_tipo_automezzo,
            ta.descrizione as tipo_automezzo,
            attr.id as id_attrezzatura,
            attr.modello as modello_attrezzatura,
            attrta.id as id_tipo_attrezzatura,
            attrta.descrizione as tipo_attrezzatura,
            v.ref_id as num_elenco_territoriale,
            v.id as id_organizzazione,
            v.denominazione as organizzazione,
            concat( s.indirizzo, ' ', cs.comune, ' (', ps.sigla, ')') as indirizzo_sede,
            s.tipo as tipo_sede,
            CASE WHEN i.stato = 0 THEN 'In attesa di conferma'
                 WHEN i.stato = 1 THEN 'Confermato'
                 WHEN i.stato = 2 THEN 'Rifiutato'
                 WHEN i.stato = 3 THEN 'Chiuso'
                 ELSE '-'
            END as stato,
            CASE WHEN i.motivazione_rifiuto = 0 THEN 'FUORI ORARIO'
                 WHEN i.motivazione_rifiuto = 1 THEN 'NON RISPONDE'
                 WHEN i.motivazione_rifiuto = 2 THEN 'MEZZO NON DISPONIBILE'
                 WHEN i.motivazione_rifiuto = 3 THEN 'SQUADRA NON DISPONIBILE'
                 WHEN i.motivazione_rifiuto = 4 THEN 'IMPEGNATA CON ALTRO ENTE'
                 WHEN i.motivazione_rifiuto = 5 THEN 'ALTRO'
                 ELSE '-'
            END as motivazione_rifiuto,
            i.note,
            e.lat,
            e.lon,
            ST_MakePoint(e.lon, e.lat) as geom,
            ARRAY_TO_STRING(ARRAY_AGG( distinct agg_a.descrizione ), ', ') as aggregatore_automezzi,
            ARRAY_TO_STRING(ARRAY_AGG( distinct agg_attr.descrizione ), ', ')  as aggregatore_attrezzature
            FROM utl_ingaggio i
            LEFT JOIN utl_evento e ON e.id = i.idevento
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN evt_gestore_evento g ON g.id = e.id_gestore_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN loc_provincia p ON p.id = c.id_provincia
            LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
            LEFT JOIN utl_automezzo_tipo ta ON ta.id = a.idtipo
            LEFT JOIN utl_attrezzatura attr ON attr.id = i.idattrezzatura
            LEFT JOIN utl_attrezzatura_tipo attrta ON attrta.id = attr.idtipo
            LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
            LEFT JOIN vol_sede s ON s.id = i.idsede
            LEFT JOIN loc_comune cs ON cs.id = s.comune
            LEFT JOIN loc_provincia ps ON ps.id = cs.id_provincia
            LEFT JOIN con_aggregatore_tipologie_tipologie c_agga ON c_agga.id_tipo_automezzo = ta.id
            LEFT JOIN con_aggregatore_tipologie_tipologie c_aggattr ON c_aggattr.id_tipo_attrezzatura = ta.id
            LEFT JOIN utl_aggregatore_tipologie agg_a ON agg_a.id = c_agga.id_aggregatore
            LEFT JOIN utl_aggregatore_tipologie agg_attr ON agg_attr.id = c_aggattr.id_aggregatore
            GROUP BY 
            i.id, e.id, t.id, st.id, g.id, c.id, p.id, a.id, ta.id, attr.id, attrta.id, v.id, s.id, cs.id, ps.id
            
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_report_attivazioni")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_095729_create_view_for_report_attivazioni cannot be reverted.\n";

        return false;
    }
    */
}
