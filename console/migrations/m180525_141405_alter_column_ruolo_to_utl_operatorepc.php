<?php

use yii\db\Migration;

/**
 * Class m180525_141405_alter_column_ruolo_to_utl_operatorepc
 */
class m180525_141405_alter_column_ruolo_to_utl_operatorepc extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TABLE utl_operatore_pc DISABLE TRIGGER ALL")
            ->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_operatore_pc DROP COLUMN ruolo CASCADE ")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_operatore_pc_ruolo")
            ->execute();

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_operatore_pc_ruolo AS ENUM ('Operatore','Volontario', 'VF', 'Dirigente', 'Funzionario')")
                ->execute();
        }

        $this->addColumn('utl_operatore_pc', 'ruolo', 'utl_operatore_pc_ruolo');


        Yii::$app->db->createCommand("ALTER TABLE utl_operatore_pc ENABLE TRIGGER ALL")
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE utl_operatore_pc DISABLE TRIGGER ALL")
            ->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_operatore_pc DROP COLUMN ruolo CASCADE ")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_operatore_pc_ruolo")
            ->execute();

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_operatore_pc_ruolo AS ENUM ('Operatore','Volontario', 'VF', 'Dirigente', 'Funzionario')")
                ->execute();
        }

        $this->addColumn('utl_operatore_pc', 'ruolo', 'utl_operatore_pc_ruolo');


        Yii::$app->db->createCommand("ALTER TABLE utl_operatore_pc ENABLE TRIGGER ALL")
            ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_141405_alter_column_ruolo_to_utl_operatorepc cannot be reverted.\n";

        return false;
    }
    */
}
