<?php

use yii\db\Migration;

/**
 * Class m180522_165203_add_columns_to_richiesta_canadair
 */
class m180522_165203_add_columns_to_richiesta_canadair extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_canadair', 'engaged', $this->boolean()->defaultValue(false));
        $this->addColumn('richiesta_canadair', 'codice_canadair', $this->string(100));
        $this->addColumn('richiesta_canadair', 'motivo_rifiuto', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'richiesta_canadair', 'engaged' );
        $this->dropColumn( 'richiesta_canadair', 'codice_canadair' );
        $this->dropColumn( 'richiesta_canadair', 'motivo_rifiuto' );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180522_165203_add_columns_to_richiesta_canadair cannot be reverted.\n";

        return false;
    }
    */
}
