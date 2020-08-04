<?php

use yii\db\Migration;

/**
 * Class m180502_085426_add_refung_column_to_volontario_ingaggio
 */
class m180502_085426_add_refung_column_to_volontario_ingaggio extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('con_volontario_ingaggio', 'refund', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('con_volontario_ingaggio', 'refund');
    }

}
