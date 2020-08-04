<?php

use yii\db\Migration;

/**
 * Class m181214_091956_alter_tables_for_geom_allerte
 */
class m181214_091956_alter_tables_for_geom_allerte extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('alm_allerta_meteo','lat',$this->double(11,5));
        $this->addColumn('alm_allerta_meteo','lon',$this->double(11,5));

        Yii::$app->db->createCommand("ALTER TABLE alm_allerta_meteo ADD COLUMN geom geometry(Point, 4326)")
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE alm_allerta_meteo DROP COLUMN geom")
            ->execute();

        $this->dropColumn('alm_allerta_meteo','lat');
        $this->dropColumn('alm_allerta_meteo','lon');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181214_091956_alter_tables_for_geom_allerte cannot be reverted.\n";

        return false;
    }
    */
}
