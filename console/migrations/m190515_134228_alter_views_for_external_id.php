<?php

use yii\db\Migration;

/**
 * Class m190515_134228_alter_views_for_external_id
 */
class m190515_134228_alter_views_for_external_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica as
            SELECT * FROM view_rubrica_strutture
            UNION
            SELECT * FROM view_rubrica_enti
            UNION
            SELECT * FROM view_rubrica_organizzazioni
            UNION
            SELECT * FROM view_rubrica_contatti_rubrica
            UNION
            SELECT * FROM view_rubrica_operatore_pc
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
        echo "m190515_134228_alter_views_for_external_id cannot be reverted.\n";

        return false;
    }
    */
}
