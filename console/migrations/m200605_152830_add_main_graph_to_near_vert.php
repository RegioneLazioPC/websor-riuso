<?php

use yii\db\Migration;

/**
 * Class m200605_152830_add_main_graph_to_near_vert
 */
class m200605_152830_add_main_graph_to_near_vert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            ALTER TABLE routing.osm_ways_vertices_pgr 
                ADD COLUMN main_network BOOLEAN DEFAULT true;
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE INDEX osm_ways_vertices_pgr_main_network_idx ON routing.osm_ways_vertices_pgr(main_network);
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("
            DROP INDEX routing.osm_ways_vertices_pgr_main_network_idx;
            ")->execute();
        Yii::$app->db->createCommand("
            ALTER TABLE routing.osm_ways_vertices_pgr 
                DROP COLUMN main_network;
            ")->execute();
    }


}
