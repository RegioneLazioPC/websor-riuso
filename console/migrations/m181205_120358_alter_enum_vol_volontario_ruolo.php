<?php

use yii\db\Migration;

/**
 * Class m181205_120358_alter_enum_vol_volontario_ruolo
 */
class m181205_120358_alter_enum_vol_volontario_ruolo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("ALTER TYPE vol_volontario_ruolo ADD VALUE IF NOT EXISTS \"Tesoriere\"");
        Yii::$app->db->createCommand("ALTER TYPE vol_volontario_ruolo ADD VALUE IF NOT EXISTS \"Componente Direttivo\"");
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
        echo "m181205_120358_alter_enum_vol_volontario_ruolo cannot be reverted.\n";

        return false;
    }
    */
}
