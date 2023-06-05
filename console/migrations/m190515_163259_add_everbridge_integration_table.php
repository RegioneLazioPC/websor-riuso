<?php

use yii\db\Migration;

/**
 * Class m190515_163259_add_everbridge_integration_table
 */
class m190515_163259_add_everbridge_integration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_view_rubrica_everbridge_n_record', [
            'id' => $this->primaryKey(),
            'identificativo' => $this->string(),
            'n_records' => $this->integer()
        ]);


        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_identificativo_con_rubrica_everbridge_n_records ON con_view_rubrica_everbridge_n_record (
            identificativo)")
        ->execute();

        $this->addColumn('mas_single_send', 'id_feedback', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("
            DROP INDEX IF EXISTS unique_identificativo_con_rubrica_everbridge_n_records")
        ->execute();


        $this->dropColumn('mas_single_send', 'id_feedback', $this->string());
        $this->dropTable('con_view_rubrica_everbridge_n_record');
    }

    /*
        // Use up()/down() to run migration code without a transaction.
        public function up()
        {

        }

        public function down()
        {
            echo "m190515_163259_add_everbridge_integration_table cannot be reverted.\n";

            return false;
        }
    */
}
