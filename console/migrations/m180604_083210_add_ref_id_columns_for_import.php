<?php

use yii\db\Migration;

/**
 * Class m180604_083210_add_ref_id_columns_for_import
 */
class m180604_083210_add_ref_id_columns_for_import extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_automezzo', 'ref_id', $this->integer());
        $this->addColumn('utl_attrezzatura', 'ref_id', $this->integer());
        $this->addColumn('vol_organizzazione', 'ref_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_automezzo', 'ref_id');
        $this->dropColumn('utl_attrezzatura', 'ref_id');
        $this->dropColumn('vol_organizzazione', 'ref_id');
    }

}
