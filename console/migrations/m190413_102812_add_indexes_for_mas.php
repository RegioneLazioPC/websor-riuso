<?php

use yii\db\Migration;

/**
 * Class m190413_102812_add_indexes_for_mas
 */
class m190413_102812_add_indexes_for_mas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            CREATE INDEX idx_id_rubrica_contatto ON mas_single_send (id_rubrica_contatto);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE INDEX idx_tipo_rubrica_contatto ON mas_single_send (tipo_rubrica_contatto);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE INDEX idx_invio ON mas_single_send (id_invio);
            ")->execute();


        Yii::$app->db->createCommand("
            CREATE INDEX idx_id_rubrica_contatto_con ON con_mas_invio_contact (id_rubrica_contatto);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE INDEX idx_tipo_rubrica_contatto_con ON con_mas_invio_contact (tipo_rubrica_contatto);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE INDEX idx_invio_con ON con_mas_invio_contact (id_invio);
            ")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("
            DROP INDEX idx_id_rubrica_contatto
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_tipo_rubrica_contatto
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_invio
            ")->execute();


        Yii::$app->db->createCommand("
            DROP INDEX idx_id_rubrica_contatto_con
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_tipo_rubrica_contatto_con
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_invio_con
            ")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190413_102812_add_indexes_for_mas cannot be reverted.\n";

        return false;
    }
    */
}
