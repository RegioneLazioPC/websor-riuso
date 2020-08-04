<?php

use yii\db\Migration;

/**
 * Class m190704_143212_add_view_for_routing_without_materialized
 */
class m190704_143212_add_view_for_routing_without_materialized extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            CREATE OR REPLACE VIEW view_routing_organizzazioni AS 
            SELECT
                f.id AS id_sede,
                v.id AS id_vert,
                v.the_geom::geography <-> f.geom::geography AS dist,
                v.the_geom AS geom_vert
               FROM vol_sede f
               CROSS JOIN LATERAL(
                select id, the_geom
                from routing.osm_ways_vertices_pgr
                order by f.geom <-> routing.osm_ways_vertices_pgr.the_geom
               limit 1
               ) v
              WHERE (v.the_geom::geography <-> f.geom::geography) IS NOT NULL
              ORDER BY f.id, (v.the_geom::geography <-> f.geom::geography);")->execute();
        
        Yii::$app->db->createCommand("
            CREATE INDEX osm_ways_vertices_pgr_the_geom_geo_idx ON routing.osm_ways_vertices_pgr 
            USING GIST ( (the_geom::geography ) );")->execute();
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_routing_organizzazioni;")->execute();
        
        Yii::$app->db->createCommand("DROP INDEX routing.osm_ways_vertices_pgr_the_geom_geo_idx;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190704_143212_add_view_for_routing_without_materialized cannot be reverted.\n";

        return false;
    }
    */
}
