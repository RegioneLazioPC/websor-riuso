<?php

use yii\db\Migration;

/**
 * Class m190520_145825_add_comuni_geom
 */
class m190520_145825_add_comuni_geom extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*
        shp2pgsql -s SRID console/data/locations_shape/CMProv2016_WGS84_g/CMprov2016_WGS84_g.shp public.loc_provincia_geom | psql -h localhost -d pclazioweb -U homestead
         */
        $import = file_get_contents(__DIR__ . '/../data/locations_shape/comuni_geom.dmp', true);

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
        Yii::$app->db->createCommand("DROP TABLE loc_comuni_geom")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190520_145825_add_comuni_geom cannot be reverted.\n";

        return false;
    }
    */
}
