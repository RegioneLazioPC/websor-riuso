<?php

use yii\db\Migration;

/**
 * Class m190516_102934_add_delivery_path
 */
class m190516_102934_add_delivery_path extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->addColumn( 'con_view_rubrica_everbridge_ext_ids', 'delivery_path', $this->string() );
        
        /*Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_identificativo_con_rubrica_everbridge_ext_ids ON con_view_rubrica_everbridge_ext_ids (
            contatto)")
        ->execute();*/

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /*Yii::$app->db->createCommand("
            DROP INDEX IF EXISTS unique_identificativo_con_rubrica_everbridge_ext_ids")
        ->execute();*/

        $this->dropColumn( 'con_view_rubrica_everbridge_ext_ids', 'delivery_path' );

        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_102934_add_delivery_path cannot be reverted.\n";

        return false;
    }
    */
}
