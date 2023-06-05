<?php

use yii\db\Migration;

/**
 * Class m220727_094612_create_view_for_attivazione_mezzi
 */
class m220727_094612_create_view_for_attivazione_mezzi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_risorse_schieramenti AS 
            select 
            concat('mezzo_', ua.id) as uid,
            ua.id,
            ua.targa as identifier,
            ua.idtipo,
            ua.meta as _meta,
            'mezzo' as tipo,
            vo.denominazione as organizzazione,
            vo.ref_id as num_elenco_territoriale,
            ua.idorganizzazione as id_organizzazione,
            ua.idsede as id_sede,
            array_to_string( array_agg(distinct vs.descrizione), ', ', '') as schieramento
            from con_mezzo_schieramento cms 
            left join utl_automezzo ua on ua.id = cms.id_utl_automezzo
            left join vol_schieramento vs on vs.id = cms.id_vol_schieramento 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione 
            where 
                cms.id is not null 
                and (vs.data_validita is null or vs.data_validita = current_date)
            group by ua.id, vo.id
            UNION
            select
            concat('attrezzatura_', ua.id) as uid, 
            ua.id,
            ua.modello as identifier,
            ua.idtipo,
            ua.meta as _meta,
            'attrezzatura' as tipo,
            vo.denominazione as organizzazione,
            vo.ref_id as num_elenco_territoriale,
            ua.idorganizzazione as id_organizzazione,
            ua.idsede as id_sede,
            array_to_string( array_agg(distinct vs.descrizione), ', ', '') as schieramento
            from con_attrezzatura_schieramento cas 
            left join utl_attrezzatura ua on ua.id = cas.id_utl_attrezzatura
            left join vol_schieramento vs on vs.id = cas.id_vol_schieramento 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione 
            where 
                cas.id is not null 
                and (vs.data_validita is null or vs.data_validita = current_date)
            group by ua.id, vo.id;
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_risorse_schieramenti")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220727_094612_create_view_for_attivazione_mezzi cannot be reverted.\n";

        return false;
    }
    */
}
