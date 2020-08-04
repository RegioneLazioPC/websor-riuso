<?php

use yii\db\Migration;

/**
 * Class m200122_100126_add_zone_allerta_to_allerta
 */
class m200122_100126_add_zone_allerta_to_allerta extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('alm_allerta_meteo', 'zone_allerta', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('alm_allerta_meteo', 'zone_allerta');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_100126_add_zone_allerta_to_allerta cannot be reverted.\n";

        return false;
    }
    */
}
