<?php

use yii\db\Migration;

/**
 * Class m200120_101400_create_zone_allerta_tables
 */
class m200120_101400_create_zone_allerta_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('alm_zona_allerta', [
            'id' => $this->primaryKey(),
            'code' => $this->string(1),
            'nome' => $this->string()
        ]);

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX ON loc_comune (codistat);
            ")->execute();
        
        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX ON alm_zona_allerta (code);
            ")->execute();

        $this->createTable('con_zona_allerta_comune', [
            'id' => $this->primaryKey(),
            'id_alm_zona_allerta' => $this->integer(),
            'codistat_comune' => $this->string(),
        ]);

        $this->addForeignKey(
            'fk_zona_allerta_comune_comune',
            'con_zona_allerta_comune',
            'codistat_comune',
            'loc_comune',
            'codistat',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_zona_allerta_comune_zona',
            'con_zona_allerta_comune',
            'id_alm_zona_allerta',
            'alm_zona_allerta',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('con_zona_allerta_comune');

        Yii::$app->db->createCommand("DROP INDEX loc_comune_codistat_idx;")->execute();

        Yii::$app->db->createCommand("DROP INDEX alm_zona_allerta_code_idx;")->execute();
        
        $this->dropTable('alm_zona_allerta');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200120_101400_create_zone_allerta_tables cannot be reverted.\n";

        return false;
    }
    */
}
