<?php

use yii\db\Migration;

/**
 * Handles the creation of table `view_only_orgs`.
 */
class m190605_152431_create_view_only_orgs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW view_organizzazioni_attive as
            SELECT o.id,
                o.ref_id AS num_elenco_territoriale,
                o.denominazione,
                vt.tipologia
            FROM vol_organizzazione o
                LEFT JOIN vol_tipo_organizzazione vt ON vt.id = o.id_tipo_organizzazione
            WHERE (o.stato_iscrizione = 3);")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_organizzazioni_attive")->execute();
    }
}
