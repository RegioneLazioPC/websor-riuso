<?php

use yii\db\Migration;

/**
 * Class m200212_151230_add_column_ambito_vol_organizzazione
 */
class m200212_151230_add_column_ambito_vol_organizzazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vol_organizzazione', 'ambito', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_organizzazione', 'ambito');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200212_151230_add_column_ambito_vol_organizzazione cannot be reverted.\n";

        return false;
    }
    */
}
