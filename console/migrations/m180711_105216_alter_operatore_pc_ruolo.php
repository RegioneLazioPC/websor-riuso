<?php

use yii\db\Migration;
use common\models\UtlOperatorePc;
/**
 * Class m180711_105216_alter_operatore_pc_ruolo
 */
class m180711_105216_alter_operatore_pc_ruolo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $operatori = UtlOperatorePc::find()->asArray()->all();

        $this->dropColumn('utl_operatore_pc', 'ruolo');

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("DROP TYPE utl_operatore_pc_ruolo")
            ->execute();
        }
        
        $this->addColumn('utl_operatore_pc', 'ruolo', $this->string(255));
        foreach ($operatori as $operatore)  {
            echo "operatore ".$operatore['ruolo']."\n";
            //$operatore->save();
            $o = UtlOperatorePc::findOne($operatore['id']);
            if($o) :
                $o->ruolo = $operatore['ruolo'];
                $o->save();
            endif;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $operatori = UtlOperatorePc::find()->asArray()->all();
        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_operatore_pc_ruolo AS ENUM ('Operatore','Volontario', 'VF', 'Dirigente', 'Funzionario','Admin')")
            ->execute();
        }
        $this->dropColumn('utl_operatore_pc', 'ruolo');
        $this->addColumn('utl_operatore_pc', 'ruolo', 'utl_operatore_pc_ruolo');
        
        foreach ($operatori as $operatore)  {
            echo "operatore ".$operatore['ruolo']."\n";
            //$operatore->save();
            $o = UtlOperatorePc::findOne($operatore['id']);
            if($o) :
                $o->ruolo = $operatore['ruolo'];
                $o->save();
            endif;
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180711_105216_alter_operatore_pc_ruolo cannot be reverted.\n";

        return false;
    }
    */
}
