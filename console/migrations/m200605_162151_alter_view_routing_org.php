<?php

use yii\db\Migration;

/**
 * Class m200605_162151_alter_view_routing_org
 */
class m200605_162151_alter_view_routing_org extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_routing_organizzazioni")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_routing_organizzazioni AS
             SELECT f.id AS id_sede,
                v.id AS id_vert,
                ((v.the_geom)::geography <-> (f.geom)::geography) AS dist,
                v.the_geom AS geom_vert
               FROM (vol_sede f
                 CROSS JOIN LATERAL ( SELECT osm_ways_vertices_pgr.id,
                        osm_ways_vertices_pgr.the_geom
                       FROM routing.osm_ways_vertices_pgr
                       WHERE main_network = true
                      ORDER BY (f.geom <-> osm_ways_vertices_pgr.the_geom)
                     LIMIT 1) v)
              WHERE (((v.the_geom)::geography <-> (f.geom)::geography) IS NOT NULL)
              ORDER BY f.id, ((v.the_geom)::geography <-> (f.geom)::geography);")
        ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_routing_organizzazioni")->execute();
        Yii::$app->db->createCommand("CREATE VIEW view_routing_organizzazioni AS
             SELECT f.id AS id_sede,
                v.id AS id_vert,
                ((v.the_geom)::geography <-> (f.geom)::geography) AS dist,
                v.the_geom AS geom_vert
               FROM (vol_sede f
                 CROSS JOIN LATERAL ( SELECT osm_ways_vertices_pgr.id,
                        osm_ways_vertices_pgr.the_geom
                       FROM routing.osm_ways_vertices_pgr
                      ORDER BY (f.geom <-> osm_ways_vertices_pgr.the_geom)
                     LIMIT 1) v)
              WHERE (((v.the_geom)::geography <-> (f.geom)::geography) IS NOT NULL)
              ORDER BY f.id, ((v.the_geom)::geography <-> (f.geom)::geography);")
        ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200605_162151_alter_view_routing_org cannot be reverted.\n";

        return false;
    }
    */
}
