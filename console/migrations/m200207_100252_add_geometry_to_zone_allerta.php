<?php

use yii\db\Migration;

/**
 * Class m200207_100252_add_geometry_to_zone_allerta
 */
class m200207_100252_add_geometry_to_zone_allerta extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('alm_zona_allerta', 'geom', 'geometry' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('alm_zona_allerta', 'geom' );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200207_100252_add_geometry_to_zone_allerta cannot be reverted.\n";

        return false;
    }
    */
}
