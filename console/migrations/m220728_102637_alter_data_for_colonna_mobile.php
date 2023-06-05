<?php

use yii\db\Migration;

/**
 * Class m220728_102637_alter_data_for_colonna_mobile
 */
class m220728_102637_alter_data_for_colonna_mobile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('con_mezzo_schieramento', 'date_from', $this->dateTime());
        $this->addColumn('con_mezzo_schieramento', 'date_to', $this->dateTime());
        $this->addColumn('con_attrezzatura_schieramento', 'date_from', $this->dateTime());
        $this->addColumn('con_attrezzatura_schieramento', 'date_to', $this->dateTime());

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

        $this->dropColumn('con_mezzo_schieramento', 'date_from');
        $this->dropColumn('con_mezzo_schieramento', 'date_to');
        $this->dropColumn('con_attrezzatura_schieramento', 'date_from');
        $this->dropColumn('con_attrezzatura_schieramento', 'date_to');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220728_102637_alter_data_for_colonna_mobile cannot be reverted.\n";

        return false;
    }
    */
}
