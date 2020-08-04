<?php

use yii\db\Migration;

/**
 * Class m191202_093306_add_json_column_ingaggi
 */
class m191202_093306_add_json_column_ingaggi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_ingaggio', 'static_data', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_ingaggio', 'static_data');
    }

}
