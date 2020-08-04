<?php

use yii\db\Migration;

/**
 * Class m180526_102849_add_column_closed_at_to_utl_evento
 */
class m180526_102849_add_column_closed_at_to_utl_evento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_evento', 'closed_at', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'utl_evento', 'closed_at' );
    }
    
}
