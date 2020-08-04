<?php

use yii\db\Migration;

/**
 * Class m180510_062142_alter_tipo_volontario_enum
 * 
 * Sarebbe il caso di evitare tutti questi enum
 */
class m180510_062142_alter_tipo_volontario_enum extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('vol_volontario', 'ruolo');

        Yii::$app->db->createCommand("DROP TYPE vol_volontario_ruolo")
            ->execute();

        Yii::$app->db->createCommand("CREATE TYPE vol_volontario_ruolo AS ENUM ('Presidente','Vice Presidente','Rappresentante Legale','Volontario')")
            ->execute();

        $this->addColumn('vol_volontario', 'ruolo', 'vol_volontario_ruolo');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_volontario', 'ruolo');

        Yii::$app->db->createCommand("DROP TYPE vol_volontario_ruolo")
            ->execute();

        Yii::$app->db->createCommand("CREATE TYPE vol_volontario_ruolo AS ENUM ('Presidente','Vice Presidente','Volontario')")
            ->execute();

        $this->addColumn('vol_volontario', 'ruolo', 'vol_volontario_ruolo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_062142_alter_tipo_volontario_enum cannot be reverted.\n";

        return false;
    }
    */
}
