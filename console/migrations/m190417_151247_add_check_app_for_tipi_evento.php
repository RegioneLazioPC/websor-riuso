<?php

use yii\db\Migration;

/**
 * Class m190417_151247_add_check_app_for_tipi_evento
 */
class m190417_151247_add_check_app_for_tipi_evento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_tipologia','check_app',$this->integer(1)->defaultValue(0));
        $this->addColumn('utl_tipologia','icon_date',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_tipologia','check_app');
        $this->dropColumn('utl_tipologia','icon_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190417_151247_add_check_app_for_tipi_evento cannot be reverted.\n";

        return false;
    }
    */
}
