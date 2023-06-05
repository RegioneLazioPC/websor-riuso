<?php

use yii\db\Migration;

/**
 * Class m220406_141058_add_index_to_routing_osm_ways_vertices_pgr
 */
class m220406_141058_add_index_to_routing_osm_ways_vertices_pgr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE INDEX IF NOT EXISTS osm_ways_vertices_pgr_the_geom_idx ON routing.osm_ways_vertices_pgr USING GIST(the_geom)")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP INDEX  IF EXISTS osm_ways_vertices_pgr_the_geom_idx")->execute();
    }
}
