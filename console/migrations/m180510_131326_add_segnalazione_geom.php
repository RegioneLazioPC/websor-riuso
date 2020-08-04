<?php

use yii\db\Migration;

/**
 * Class m180510_131326_add_segnalazione_geom
 */
class m180510_131326_add_segnalazione_geom extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TABLE utl_segnalazione ADD COLUMN geom geometry(Point, 4326)")
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE vol_sede DROP COLUMN geom")
            ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_131326_add_segnalazione_geom cannot be reverted.\n";

        return false;
    }
    */
}
