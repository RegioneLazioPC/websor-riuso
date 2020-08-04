<?php

use yii\db\Migration;

/**
 * Class m190516_095622_alter_con_everbridge_columns
 * 
 * ho bisogno di dare al mas anche l'external id per poter avere la notifica sul singolo recapito
 */
class m190516_095622_alter_con_everbridge_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Rimuovo la vista
         */
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica")->execute();

        /**
         * Tolgo la relazione
         */
        Yii::$app->db->createCommand("
            DROP INDEX IF EXISTS unique_identificativo_con_rubrica_everbridge_n_records")
        ->execute();

        $this->dropTable( 'con_view_rubrica_everbridge_n_record');

        $this->createTable( 'con_view_rubrica_everbridge_ext_ids', [
            'id' => $this->primaryKey(),
            'contatto' => $this->string(), // {id_contatto}_{contatto_type}
            'ext_id' => $this->string(),
            'identificativo' => $this->string()
        ] );

        /**
         * Ricreo la vista ma con gli ext_id
         */
        Yii::$app->db->createCommand("CREATE VIEW view_rubrica as
            SELECT view_rubrica_strutture.*, 
            CONCAT(view_rubrica_strutture.tipologia_riferimento, '_', view_rubrica_strutture.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_ext_ids.ext_id
            FROM view_rubrica_strutture LEFT JOIN con_view_rubrica_everbridge_ext_ids ON con_view_rubrica_everbridge_ext_ids.identificativo = CONCAT(view_rubrica_strutture.id_contatto, '_', view_rubrica_strutture.contatto_type)
            UNION
            SELECT view_rubrica_enti.*, 
            CONCAT(view_rubrica_enti.tipologia_riferimento, '_', view_rubrica_enti.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_ext_ids.ext_id 
            FROM view_rubrica_enti LEFT JOIN con_view_rubrica_everbridge_ext_ids ON con_view_rubrica_everbridge_ext_ids.identificativo = CONCAT(view_rubrica_enti.id_contatto, '_', view_rubrica_enti.contatto_type)
            UNION
            SELECT view_rubrica_organizzazioni.*, 
            CONCAT(view_rubrica_organizzazioni.tipologia_riferimento, '_', view_rubrica_organizzazioni.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_ext_ids.ext_id  
            FROM view_rubrica_organizzazioni LEFT JOIN con_view_rubrica_everbridge_ext_ids ON con_view_rubrica_everbridge_ext_ids.identificativo = CONCAT(view_rubrica_organizzazioni.id_contatto, '_', view_rubrica_organizzazioni.contatto_type)
            UNION
            SELECT view_rubrica_contatti_rubrica.*, 
            CONCAT(view_rubrica_contatti_rubrica.tipologia_riferimento, '_', view_rubrica_contatti_rubrica.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_ext_ids.ext_id 
            FROM view_rubrica_contatti_rubrica LEFT JOIN con_view_rubrica_everbridge_ext_ids ON con_view_rubrica_everbridge_ext_ids.identificativo = CONCAT(view_rubrica_contatti_rubrica.id_contatto, '_', view_rubrica_contatti_rubrica.contatto_type)
            UNION
            SELECT view_rubrica_operatore_pc.*, 
            CONCAT(view_rubrica_operatore_pc.tipologia_riferimento, '_', view_rubrica_operatore_pc.id_riferimento) as identificativo, 
            con_view_rubrica_everbridge_ext_ids.ext_id 
            FROM view_rubrica_operatore_pc  LEFT JOIN con_view_rubrica_everbridge_ext_ids ON con_view_rubrica_everbridge_ext_ids.identificativo = CONCAT(view_rubrica_operatore_pc.id_contatto, '_', view_rubrica_operatore_pc.contatto_type)
            "
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable( 'con_view_rubrica_everbridge_n_record', [
            'id' => $this->primaryKey(),
            'identificativo' => $this->string(),
            'n_records' => $this->integer()
        ] );

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_identificativo_con_rubrica_everbridge_n_records ON con_view_rubrica_everbridge_n_record (
            identificativo)")
        ->execute();

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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_095622_alter_con_everbridge_columns cannot be reverted.\n";

        return false;
    }
    */
}
