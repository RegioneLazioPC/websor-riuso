<?php

use yii\db\Migration;

/**
 * Class m210901_102102_alter_view_report
 */
class m210901_102102_alter_view_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_report_interventi_elicotteri")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_report_interventi_elicotteri AS 
            SELECT
                e.id AS id_evento,
                e.num_protocollo,
                r.dataora_decollo,
                r.dataora_atterraggio,
                DATE(r.dataora_decollo) AS data_attivazione,
                r.dataora_atterraggio - r.dataora_decollo AS tempo_volo,
                r.n_lanci,
                c.id as id_comune,
                c.comune,
                p.id as id_provincia,
                p.sigla as sigla_provincia,
                p.provincia,
                eli.targa as elicottero,
                r.engaged
            FROM
                richiesta_elicottero r
                LEFT JOIN utl_evento e ON e.id = r.idevento
                LEFT JOIN loc_comune c ON c.id = e.idcomune
                LEFT JOIN loc_provincia p ON p.id = c.id_provincia
                LEFT JOIN utl_automezzo eli ON eli.id = r.id_elicottero
            WHERE
                r.engaged = TRUE;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_report_interventi_elicotteri")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_report_interventi_elicotteri AS 
            SELECT e.id as id_evento, e.num_protocollo, r.dataora_decollo, r.dataora_atterraggio, DATE(r.dataora_decollo) as data_attivazione, r.dataora_atterraggio-r.dataora_decollo as tempo_volo, r.n_lanci, r.engaged FROM richiesta_elicottero r
                LEFT JOIN utl_evento e ON e.id = r.idevento
                WHERE r.engaged = true")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_102102_alter_view_report cannot be reverted.\n";

        return false;
    }
    */
}
