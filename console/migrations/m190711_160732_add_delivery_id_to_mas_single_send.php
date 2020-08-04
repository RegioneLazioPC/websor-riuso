<?php

use yii\db\Migration;

/**
 * Class m190711_160732_add_delivery_id_to_mas_single_send
 */
class m190711_160732_add_delivery_id_to_mas_single_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_single_send', '_id_delivery', $this->integer() );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'mas_single_send', '_id_delivery' );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190711_160732_add_delivery_id_to_mas_single_send cannot be reverted.\n";

        return false;
    }
    */
}
