<?php

use yii\db\Migration;

/**
 * Class m180525_090159_alter_column_stato_to_utl_evento
 */
class m180525_090159_alter_column_stato_to_utl_evento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        Yii::$app->db->createCommand("ALTER TABLE utl_evento DISABLE TRIGGER ALL")
                    ->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_evento DROP COLUMN stato CASCADE ")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_evento_stato")
            ->execute();

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_evento_stato AS ENUM ('Non gestito','In gestione', 'Chiuso')")
                ->execute();
        }
        
        $this->addColumn('utl_evento', 'stato', 'utl_evento_stato');


        Yii::$app->db->createCommand("ALTER TABLE utl_evento ENABLE TRIGGER ALL")
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("ALTER TABLE utl_evento DISABLE TRIGGER ALL")
                    ->execute();

        Yii::$app->db->createCommand("ALTER TABLE utl_evento DROP COLUMN stato CASCADE ")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_evento_stato")
            ->execute();

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_evento_stato AS ENUM ('Non gestito','In gestione', 'Chiuso')")
                ->execute();
        }
        
        $this->addColumn('utl_evento', 'stato', 'utl_evento_stato');


        Yii::$app->db->createCommand("ALTER TABLE utl_evento ENABLE TRIGGER ALL")
            ->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_090159_alter_column_stato_to_utl_evento cannot be reverted.\n";

        return false;
    }
    */
}
