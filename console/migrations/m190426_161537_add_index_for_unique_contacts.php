<?php

use yii\db\Migration;

/**
 * Class m190426_161537_add_index_for_unique_contacts
 */
class m190426_161537_add_index_for_unique_contacts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX unique_con_mas_invio_contact_contacts ON con_mas_invio_contact (
            id_rubrica_contatto,
            tipo_rubrica_contatto,
            valore_rubrica_contatto,
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
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190426_161537_add_index_for_unique_contacts cannot be reverted.\n";

        return false;
    }
    */
}
