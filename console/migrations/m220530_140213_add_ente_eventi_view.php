<?php

use yii\db\Migration;

/**
 * Class m220530_140213_add_ente_eventi_view
 */
class m220530_140213_add_ente_eventi_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_enti_task_evento AS 
            SELECT t.id as id_utl_task, t.descrizione as descrizione_utl_task, e.* FROM utl_task t
            LEFT JOIN con_operatore_task ct ON ct.idtask = t.id 
            LEFT JOIN utl_evento e ON e.id = ct.idevento
            GROUP BY t.id, e.id")
        ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_enti_task_evento")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220530_140213_add_ente_eventi_view cannot be reverted.\n";

        return false;
    }
    */
}
