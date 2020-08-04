<?php

use yii\db\Migration;

/**
 * Class m200316_110440_set_vol_volontario_datore_di_lavoro
 */
class m200316_110440_set_vol_volontario_datore_di_lavoro extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vol_volontario', 'datore_di_lavoro', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_volontario', 'datore_di_lavoro');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200316_110440_set_vol_volontario_datore_di_lavoro cannot be reverted.\n";

        return false;
    }
    */
}
