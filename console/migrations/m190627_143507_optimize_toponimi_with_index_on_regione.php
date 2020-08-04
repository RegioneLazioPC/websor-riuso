<?php

use yii\db\Migration;

/**
 * Class m190627_143507_optimize_toponimi_with_index_on_regione
 */
class m190627_143507_optimize_toponimi_with_index_on_regione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE INDEX toponimi_regione_idx ON toponimi_igm_geom (cod_regione);")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP INDEX toponimi_regione_idx;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_143507_optimize_toponimi_with_index_on_regione cannot be reverted.\n";

        return false;
    }
    */
}
