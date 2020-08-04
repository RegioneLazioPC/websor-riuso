<?php

use yii\db\Migration;

/**
 * Class m180509_143616_update_sedi_table_for_production
 */
class m180509_143616_update_sedi_table_for_production extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vol_sede', 'cap', $this->string(5));
        $this->addColumn('vol_sede', 'altro_fax', $this->string(50));
        $this->addColumn('vol_sede', 'coord_x', $this->float() );
        $this->addColumn('vol_sede', 'coord_y', $this->float() );
        $this->addColumn('vol_organizzazione', 'data_costituzione', $this->date() );

        $this->alterColumn('vol_organizzazione', 'num_albo_regionale', $this->string(255) );
        $this->alterColumn('vol_organizzazione', 'data_albo_regionale', $this->date() );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_sede', 'cap');
        $this->dropColumn('vol_sede', 'altro_fax');
        $this->dropColumn('vol_sede', 'coord_x');
        $this->dropColumn('vol_sede', 'coord_y');
        $this->dropColumn('vol_organizzazione', 'data_costituzione');

        $this->alterColumn('vol_organizzazione', 'num_albo_regionale', $this->string(4) );
        $this->alterColumn('vol_organizzazione', 'data_albo_regionale', $this->timestamp() );
    }

}
