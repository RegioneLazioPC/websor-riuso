<?php

use yii\db\Migration;

/**
 * Class m210831_105548_add_view_interventi_elicotteri
 */
class m210831_105548_add_view_interventi_elicotteri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_report_interventi_elicotteri AS 
            SELECT e.id as id_evento, e.num_protocollo, r.dataora_decollo, r.dataora_atterraggio, DATE(r.dataora_decollo) as data_attivazione, r.dataora_atterraggio-r.dataora_decollo as tempo_volo, r.n_lanci, r.engaged FROM richiesta_elicottero r
                LEFT JOIN utl_evento e ON e.id = r.idevento
                WHERE r.engaged = true")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_report_interventi_elicotteri")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210831_105548_add_view_interventi_elicotteri cannot be reverted.\n";

        return false;
    }
    */
}
