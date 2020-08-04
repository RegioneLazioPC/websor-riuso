<?php

use yii\db\Migration;

/**
 * Class m190516_084508_add_n_record_in_view_rubrica
 */
class m190516_084508_add_n_record_in_view_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica as
            SELECT view_rubrica_strutture.*, 
            CONCAT(view_rubrica_strutture.tipologia_riferimento, '_', view_rubrica_strutture.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_n_record.n_records
            FROM view_rubrica_strutture LEFT JOIN con_view_rubrica_everbridge_n_record ON con_view_rubrica_everbridge_n_record.identificativo = CONCAT(view_rubrica_strutture.tipologia_riferimento, '_', view_rubrica_strutture.id_riferimento)
            UNION
            SELECT view_rubrica_enti.*, 
            CONCAT(view_rubrica_enti.tipologia_riferimento, '_', view_rubrica_enti.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_n_record.n_records 
            FROM view_rubrica_enti LEFT JOIN con_view_rubrica_everbridge_n_record ON con_view_rubrica_everbridge_n_record.identificativo = CONCAT(view_rubrica_enti.tipologia_riferimento, '_', view_rubrica_enti.id_riferimento)
            UNION
            SELECT view_rubrica_organizzazioni.*, 
            CONCAT(view_rubrica_organizzazioni.tipologia_riferimento, '_', view_rubrica_organizzazioni.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_n_record.n_records  
            FROM view_rubrica_organizzazioni LEFT JOIN con_view_rubrica_everbridge_n_record ON con_view_rubrica_everbridge_n_record.identificativo = CONCAT(view_rubrica_organizzazioni.tipologia_riferimento, '_', view_rubrica_organizzazioni.id_riferimento)
            UNION
            SELECT view_rubrica_contatti_rubrica.*, 
            CONCAT(view_rubrica_contatti_rubrica.tipologia_riferimento, '_', view_rubrica_contatti_rubrica.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_n_record.n_records 
            FROM view_rubrica_contatti_rubrica LEFT JOIN con_view_rubrica_everbridge_n_record ON con_view_rubrica_everbridge_n_record.identificativo = CONCAT(view_rubrica_contatti_rubrica.tipologia_riferimento, '_', view_rubrica_contatti_rubrica.id_riferimento)
            UNION
            SELECT view_rubrica_operatore_pc.*, 
            CONCAT(view_rubrica_operatore_pc.tipologia_riferimento, '_', view_rubrica_operatore_pc.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_n_record.n_records 
            FROM view_rubrica_operatore_pc  LEFT JOIN con_view_rubrica_everbridge_n_record ON con_view_rubrica_everbridge_n_record.identificativo = CONCAT(view_rubrica_operatore_pc.tipologia_riferimento, '_', view_rubrica_operatore_pc.id_riferimento)
            "
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica as
            SELECT view_rubrica_strutture.*, CONCAT(view_rubrica_strutture.tipologia_riferimento, '_', view_rubrica_strutture.id_riferimento) as identificativo FROM view_rubrica_strutture
            UNION
            SELECT view_rubrica_enti.*, CONCAT(view_rubrica_enti.tipologia_riferimento, '_', view_rubrica_enti.id_riferimento) as identificativo FROM view_rubrica_enti
            UNION
            SELECT view_rubrica_organizzazioni.*, CONCAT(view_rubrica_organizzazioni.tipologia_riferimento, '_', view_rubrica_organizzazioni.id_riferimento) as identificativo  FROM view_rubrica_organizzazioni
            UNION
            SELECT view_rubrica_contatti_rubrica.*, CONCAT(view_rubrica_contatti_rubrica.tipologia_riferimento, '_', view_rubrica_contatti_rubrica.id_riferimento) as identificativo FROM view_rubrica_contatti_rubrica
            UNION
            SELECT view_rubrica_operatore_pc.*, CONCAT(view_rubrica_operatore_pc.tipologia_riferimento, '_', view_rubrica_operatore_pc.id_riferimento) as identificativo FROM view_rubrica_operatore_pc
            "
        )->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_084508_add_n_record_in_view_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
