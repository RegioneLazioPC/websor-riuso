<?php

use yii\db\Migration;

/**
 * Class m181213_205333_add_column_sos_to_utl_segnalazione
 */
class m181213_205333_add_column_sos_to_utl_segnalazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("utl_segnalazione", "sos", $this->boolean()->defaultValue(false) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("utl_segnalazione", "sos");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181213_205333_add_column_sos_to_utl_segnalazione cannot be reverted.\n";

        return false;
    }
    */
}
