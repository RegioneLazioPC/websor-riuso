<?php

use yii\db\Migration;

/**
 * Class m200316_123531_update_view_volontari_attivazioni
 */
class m200316_123531_update_view_volontari_attivazioni extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_volontari_attivazioni")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_volontari_attivazioni AS 
            SELECT 
            concat(i.id, '_', v.id) as id,
            o.ref_id as num_elenco_territoriale,
            o.denominazione,
            e.num_protocollo as protocollo_evento,
            null AS protocollo_fronte,
            e.id AS id_evento,
            null as id_fronte,
            CASE WHEN e.luogo is not null AND e.luogo != '' 
                THEN e.luogo 
                ELSE e.indirizzo
            END AS localita,
            c.comune,
            p.sigla as provincia,
            tp.id as id_tipologia,
            tp.tipologia,
            stp.id as id_sottotipologia,
            stp.tipologia as sottotipologia,
            a.id as id_mezzo,
            ta.descrizione as mezzo,
            a.targa as targa,
            att.id as id_attrezzatura,
            tatt.descrizione as attrezzatura,
            att.modello as modello,
            CASE 
                WHEN a.id is not null THEN concat(ta.descrizione, ', targa: ', a.targa)
                WHEN att.id is not null THEN concat(tatt.descrizione, ', modello: ', att.modello)
                ELSE ''
            END AS full_mezzo,
            i.id as id_attivazione,
            i.created_at as creazione,
            i.closed_at as chiusura,
            i.stato,
            v.id as id_volontario,
            ana.id as id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
             FROM 
            utl_evento e
            LEFT JOIN utl_evento eg ON eg.id = e.idparent
            LEFT JOIN utl_tipologia tp ON tp.id = e.tipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN loc_provincia p ON p.id = c.id_provincia
            LEFT JOIN utl_tipologia stp ON stp.id = e.sottotipologia_evento
            LEFT JOIN utl_ingaggio i ON i.idevento = e.id
            LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
            LEFT JOIN utl_automezzo_tipo ta ON ta.id = a.idtipo
            LEFT JOIN utl_attrezzatura att ON att.id = i.idattrezzatura
            LEFT JOIN utl_attrezzatura_tipo tatt ON tatt.id = att.idtipo
            LEFT JOIN con_volontario_ingaggio ci ON ci.id_ingaggio = i.id 
            LEFT JOIN vol_volontario v ON v.id = ci.id_volontario
            LEFT JOIN utl_anagrafica ana ON ana.id = v.id_anagrafica
            LEFT JOIN vol_organizzazione o ON o.id = i.idorganizzazione
            WHERE ana.id is not null AND e.idparent is null
            UNION ALL 
            SELECT 
            concat(i.id, '_', v.id) as id,
            o.ref_id as num_elenco_territoriale,
            o.denominazione,
            eg.num_protocollo AS protocollo_evento,
            e.num_protocollo AS protocollo_fronte,
            eg.id AS id_evento,
            e.id AS id_fronte,
            CASE WHEN e.luogo is not null AND e.luogo != '' 
                THEN e.luogo 
                ELSE e.indirizzo
            END AS localita,
            c.comune,
            p.sigla as provincia,
            tp.id as id_tipologia,
            tp.tipologia,
            stp.id as id_sottotipologia,
            stp.tipologia as sottotipologia,
            a.id as id_mezzo,
            ta.descrizione as mezzo,
            a.targa as targa,
            att.id as id_attrezzatura,
            tatt.descrizione as attrezzatura,
            att.modello as modello,
            CASE 
                WHEN a.id is not null THEN concat(ta.descrizione, ', targa: ', a.targa)
                WHEN att.id is not null THEN concat(tatt.descrizione, ', modello: ', att.modello)
                ELSE ''
            END AS full_mezzo,
            i.id as id_attivazione,
            i.created_at as creazione,
            i.closed_at as chiusura,
            i.stato,
            v.id as id_volontario,
            ana.id as id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale,
            v.datore_di_lavoro,
            ci.refund
             FROM 
            utl_evento e
            LEFT JOIN utl_evento eg ON eg.id = e.idparent
            LEFT JOIN utl_tipologia tp ON tp.id = e.tipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN loc_provincia p ON p.id = c.id_provincia
            LEFT JOIN utl_tipologia stp ON stp.id = e.sottotipologia_evento
            LEFT JOIN utl_ingaggio i ON i.idevento = e.id
            LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
            LEFT JOIN utl_automezzo_tipo ta ON ta.id = a.idtipo
            LEFT JOIN utl_attrezzatura att ON att.id = i.idattrezzatura
            LEFT JOIN utl_attrezzatura_tipo tatt ON tatt.id = att.idtipo
            LEFT JOIN con_volontario_ingaggio ci ON ci.id_ingaggio = i.id 
            LEFT JOIN vol_volontario v ON v.id = ci.id_volontario
            LEFT JOIN utl_anagrafica ana ON ana.id = v.id_anagrafica
            LEFT JOIN vol_organizzazione o ON o.id = i.idorganizzazione
            WHERE ana.id is not null AND e.idparent is not null
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_volontari_attivazioni")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_volontari_attivazioni AS 
            SELECT 
            concat(i.id, '_', v.id) as id,
            o.ref_id as num_elenco_territoriale,
            o.denominazione,
            e.num_protocollo as protocollo_evento,
            null AS protocollo_fronte,
            e.id AS id_evento,
            null as id_fronte,
            CASE WHEN e.luogo is not null AND e.luogo != '' 
                THEN e.luogo 
                ELSE e.indirizzo
            END AS localita,
            c.comune,
            p.sigla as provincia,
            tp.id as id_tipologia,
            tp.tipologia,
            stp.id as id_sottotipologia,
            stp.tipologia as sottotipologia,
            a.id as id_mezzo,
            ta.descrizione as mezzo,
            a.targa as targa,
            att.id as id_attrezzatura,
            tatt.descrizione as attrezzatura,
            att.modello as modello,
            CASE 
                WHEN a.id is not null THEN concat(ta.descrizione, ', targa: ', a.targa)
                WHEN att.id is not null THEN concat(tatt.descrizione, ', modello: ', att.modello)
                ELSE ''
            END AS full_mezzo,
            i.id as id_attivazione,
            i.created_at as creazione,
            i.closed_at as chiusura,
            i.stato,
            v.id as id_volontario,
            ana.id as id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale
             FROM 
            utl_evento e
            LEFT JOIN utl_evento eg ON eg.id = e.idparent
            LEFT JOIN utl_tipologia tp ON tp.id = e.tipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN loc_provincia p ON p.id = c.id_provincia
            LEFT JOIN utl_tipologia stp ON stp.id = e.sottotipologia_evento
            LEFT JOIN utl_ingaggio i ON i.idevento = e.id
            LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
            LEFT JOIN utl_automezzo_tipo ta ON ta.id = a.idtipo
            LEFT JOIN utl_attrezzatura att ON att.id = i.idattrezzatura
            LEFT JOIN utl_attrezzatura_tipo tatt ON tatt.id = att.idtipo
            LEFT JOIN con_volontario_ingaggio ci ON ci.id_ingaggio = i.id 
            LEFT JOIN vol_volontario v ON v.id = ci.id_volontario
            LEFT JOIN utl_anagrafica ana ON ana.id = v.id_anagrafica
            LEFT JOIN vol_organizzazione o ON o.id = i.idorganizzazione
            WHERE ana.id is not null AND e.idparent is null
            UNION ALL 
            SELECT 
            concat(i.id, '_', v.id) as id,
            o.ref_id as num_elenco_territoriale,
            o.denominazione,
            eg.num_protocollo AS protocollo_evento,
            e.num_protocollo AS protocollo_fronte,
            eg.id AS id_evento,
            e.id AS id_fronte,
            CASE WHEN e.luogo is not null AND e.luogo != '' 
                THEN e.luogo 
                ELSE e.indirizzo
            END AS localita,
            c.comune,
            p.sigla as provincia,
            tp.id as id_tipologia,
            tp.tipologia,
            stp.id as id_sottotipologia,
            stp.tipologia as sottotipologia,
            a.id as id_mezzo,
            ta.descrizione as mezzo,
            a.targa as targa,
            att.id as id_attrezzatura,
            tatt.descrizione as attrezzatura,
            att.modello as modello,
            CASE 
                WHEN a.id is not null THEN concat(ta.descrizione, ', targa: ', a.targa)
                WHEN att.id is not null THEN concat(tatt.descrizione, ', modello: ', att.modello)
                ELSE ''
            END AS full_mezzo,
            i.id as id_attivazione,
            i.created_at as creazione,
            i.closed_at as chiusura,
            i.stato,
            v.id as id_volontario,
            ana.id as id_anagrafica,
            ana.nome,
            ana.cognome,
            ana.codfiscale
             FROM 
            utl_evento e
            LEFT JOIN utl_evento eg ON eg.id = e.idparent
            LEFT JOIN utl_tipologia tp ON tp.id = e.tipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN loc_provincia p ON p.id = c.id_provincia
            LEFT JOIN utl_tipologia stp ON stp.id = e.sottotipologia_evento
            LEFT JOIN utl_ingaggio i ON i.idevento = e.id
            LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
            LEFT JOIN utl_automezzo_tipo ta ON ta.id = a.idtipo
            LEFT JOIN utl_attrezzatura att ON att.id = i.idattrezzatura
            LEFT JOIN utl_attrezzatura_tipo tatt ON tatt.id = att.idtipo
            LEFT JOIN con_volontario_ingaggio ci ON ci.id_ingaggio = i.id 
            LEFT JOIN vol_volontario v ON v.id = ci.id_volontario
            LEFT JOIN utl_anagrafica ana ON ana.id = v.id_anagrafica
            LEFT JOIN vol_organizzazione o ON o.id = i.idorganizzazione
            WHERE ana.id is not null AND e.idparent is not null
            ")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200316_123531_update_view_volontari_attivazioni cannot be reverted.\n";

        return false;
    }
    */
}
