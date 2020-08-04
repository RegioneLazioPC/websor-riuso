<?php

use yii\db\Migration;

/**
 * Class m200316_114443_set_con_volontario_ingaggio_datore_di_lavoro
 */
class m200316_114443_set_con_volontario_ingaggio_datore_di_lavoro extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_volontario_ingaggio', 'datore_di_lavoro', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_volontario_ingaggio', 'datore_di_lavoro');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200316_114443_set_con_volontario_ingaggio_datore_di_lavoro cannot be reverted.\n";

        return false;
    }
    */
}
