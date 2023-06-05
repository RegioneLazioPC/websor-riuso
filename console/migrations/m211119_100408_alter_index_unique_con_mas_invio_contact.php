<?php

use yii\db\Migration;

/**
 * Class m211119_100408_alter_index_unique_con_mas_invio_contact
 */
class m211119_100408_alter_index_unique_con_mas_invio_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            DROP INDEX IF EXISTS unique_con_mas_invio_contact_contacts")
        ->execute();

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_con_mas_invio_contact_contacts ON con_mas_invio_contact (
            id_rubrica_contatto,
            tipo_rubrica_contatto,
            valore_rubrica_contatto,
            channel,
            id_invio)")
        ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("
            DROP INDEX IF EXISTS unique_con_mas_invio_contact_contacts")
        ->execute();
        
        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_con_mas_invio_contact_contacts ON con_mas_invio_contact (
            id_rubrica_contatto,
            tipo_rubrica_contatto,
            valore_rubrica_contatto,
            id_invio)")
        ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211119_100408_alter_index_unique_con_mas_invio_contact cannot be reverted.\n";

        return false;
    }
    */
}
