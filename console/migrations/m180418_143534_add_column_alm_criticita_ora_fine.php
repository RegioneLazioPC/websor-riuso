<?php

use yii\db\Migration;

/**
 * Class m180418_143534_add_column_alm_criticita_ora_fine
 */
class m180418_143534_add_column_alm_criticita_ora_fine extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('alm_criticita', 'ora_fine', $this->time());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('alm_criticita', 'ora_fine');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180418_143534_add_column_alm_criticita_ora_fine cannot be reverted.\n";

        return false;
    }
    */
}
