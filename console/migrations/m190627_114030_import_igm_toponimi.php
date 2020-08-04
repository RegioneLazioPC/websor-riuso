<?php

use yii\db\Migration;

/**
 * Class m190627_114030_import_igm_toponimi
 */
class m190627_114030_import_igm_toponimi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        ini_set('memory_limit', '-1');
        $import = file_get_contents(__DIR__ . '/../data/locations_shape/geom_toponimi.sql', true);


        $import = explode(";", $import);
        foreach ($import as $query) {            
            if($query != "") {
                Yii::$app->db->createCommand($query)->execute();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP TABLE geom_toponimi")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_114030_import_igm_toponimi cannot be reverted.\n";

        return false;
    }
    */
}
