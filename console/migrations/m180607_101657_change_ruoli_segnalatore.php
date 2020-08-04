<?php

use yii\db\Migration;

/**
 * Class m180607_101657_change_ruoli_segnalatore
 */
class m180607_101657_change_ruoli_segnalatore extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DELETE FROM utl_ruolo_segnalatore")
            ->execute();

        $data = [
            ['Vigli del fuoco'],
            ['Carabinieri forestali'],
            ['Carabinieri'],
            ['Polizia di stato'],
            ['Comune'],
            ['altro']
        ];
        Yii::$app->db
        ->createCommand()
        ->batchInsert('utl_ruolo_segnalatore', ['descrizione'], $data)
        ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_101657_change_ruoli_segnalatore cannot be reverted.\n";

        return false;
    }
    */
}
