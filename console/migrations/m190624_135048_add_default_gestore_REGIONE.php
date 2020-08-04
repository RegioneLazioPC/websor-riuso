<?php

use yii\db\Migration;

/**
 * Class m190624_135048_add_default_gestore_REGIONE
 */
class m190624_135048_add_default_gestore_REGIONE extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("INSERT INTO evt_gestore_evento(id, descrizione) VALUES(0,'REGIONE')")->execute();
        Yii::$app->db->createCommand("UPDATE utl_evento SET id_gestore_evento = 0 WHERE id_gestore_evento IS NULL")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DELETE FROM evt_gestore_evento WHERE id = 0")->execute();
        Yii::$app->db->createCommand("UPDATE utl_evento SET id_gestore_evento = null WHERE id_gestore_evento = 0")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190624_135048_add_default_gestore_REGIONE cannot be reverted.\n";

        return false;
    }
    */
}
