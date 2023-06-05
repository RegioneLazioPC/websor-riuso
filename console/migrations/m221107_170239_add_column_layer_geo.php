<?php

use yii\db\Migration;

/**
 * Class m221107_170239_add_column_layer_geo
 */
class m221107_170239_add_column_layer_geo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('geo_layer', 'srid', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('geo_layer', 'srid');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221107_170239_add_column_layer_geo cannot be reverted.\n";

        return false;
    }
    */
}
