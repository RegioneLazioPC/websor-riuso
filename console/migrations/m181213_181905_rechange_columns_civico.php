<?php

use yii\db\Migration;

/**
 * Class m181213_181905_rechange_columns_civico
 */
class m181213_181905_rechange_columns_civico extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('loc_civico','lat');
        $this->dropColumn('loc_civico','lon');
        $this->addColumn('loc_civico','lat',$this->double(11,5));
        $this->addColumn('loc_civico','lon',$this->double(11,5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('loc_civico','lat');
        $this->dropColumn('loc_civico','lon');
        $this->addColumn('loc_civico','lat',$this->double(11,5));
        $this->addColumn('loc_civico','lon',$this->double(11,5));

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181213_181905_rechange_columns_civico cannot be reverted.\n";

        return false;
    }
    */
}
