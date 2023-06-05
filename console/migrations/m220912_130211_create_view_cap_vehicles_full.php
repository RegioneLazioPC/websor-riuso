<?php

use yii\db\Migration;

/**
 * Class m220912_130211_create_view_cap_vehicles_full
 */
class m220912_130211_create_view_cap_vehicles_full extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_cap_vehicles_report AS
            select vcvh.*, vo.ref_id as numero_elenco_territoriale, vo.denominazione as organizzazione from view_cap_vehicles_history vcvh 
            left join utl_automezzo ua on ua.targa = vcvh.targa 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione;")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_cap_vehicles_report_current AS
            select vcvh.*, vo.ref_id as numero_elenco_territoriale, vo.denominazione as organizzazione from view_cap_vehicles_history vcvh 
            left join utl_automezzo ua on ua.targa = vcvh.targa 
            left join vol_organizzazione vo on vo.id = ua.idorganizzazione
            where vcvh.expires_rome_timezone > now()
            ;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles_report")->execute();
        Yii::$app->db->createCommand("DROP VIEW view_cap_vehicles_report_current")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220912_130211_create_view_cap_vehicles_full cannot be reverted.\n";

        return false;
    }
    */
}
