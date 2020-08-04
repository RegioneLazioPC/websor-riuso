<?php

use yii\db\Migration;

/**
 * Class m190627_133845_alter_igm_table_name
 */
class m190627_133845_alter_igm_table_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TABLE geom_toponimi RENAME TO toponimi_igm_geom")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE toponimi_igm_geom RENAME TO geom_toponimi")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_133845_alter_igm_table_name cannot be reverted.\n";

        return false;
    }
    */
}
