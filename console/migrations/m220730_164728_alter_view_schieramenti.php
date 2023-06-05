<?php

use yii\db\Migration;

/**
 * Class m220730_164728_alter_view_schieramenti
 */
class m220730_164728_alter_view_schieramenti extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_risorse_schieramenti")->execute();
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
            vs.descrizione as schieramento
            from con_mezzo_schieramento cms 
            left join utl_automezzo ua on ua.id = cms.id_utl_automezzo
            left join vol_schieramento vs on vs.id = cms.id_vol_schieramento 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione 
            where 
                cms.id is not null 
                and 
                (cms.date_from is null OR cms.date_from <= current_timestamp) 
                and
                ( cms.date_to is null OR cms.date_to >= current_timestamp)
            group by ua.id, vo.id, vs.id
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
            vs.descrizione as schieramento
            from con_attrezzatura_schieramento cas 
            left join utl_attrezzatura ua on ua.id = cas.id_utl_attrezzatura
            left join vol_schieramento vs on vs.id = cas.id_vol_schieramento 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione 
            where 
                cas.id is not null 
                and 
                (cas.date_from is null OR cas.date_from <= current_timestamp) 
                and
                ( cas.date_to is null OR cas.date_to >= current_timestamp)
            group by ua.id, vo.id, vs.id;
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       Yii::$app->db->createCommand("DROP VIEW view_risorse_schieramenti")->execute();
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
            vs.descrizione as schieramento
            from con_mezzo_schieramento cms 
            left join utl_automezzo ua on ua.id = cms.id_utl_automezzo
            left join vol_schieramento vs on vs.id = cms.id_vol_schieramento 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione 
            where 
                cms.id is not null 
                and (
                        (cms.date_from is null and cms.date_to is null) or 
                        (
                            cms.date_from <= NOW() AND cms.date_to >= NOW()
                        )
                    )
            group by ua.id, vo.id, vs.id
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
            vs.descrizione as schieramento
            from con_attrezzatura_schieramento cas 
            left join utl_attrezzatura ua on ua.id = cas.id_utl_attrezzatura
            left join vol_schieramento vs on vs.id = cas.id_vol_schieramento 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione 
            where 
                cas.id is not null 
                and (
                        (cas.date_from is null and cas.date_to is null) or 
                        (
                            cas.date_from <= NOW() AND cas.date_to >= NOW()
                        )
                    )
            group by ua.id, vo.id, vs.id;
            ")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220730_164728_alter_view_schieramenti cannot be reverted.\n";

        return false;
    }
    */
}
