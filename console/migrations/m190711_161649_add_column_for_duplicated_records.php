<?php

use yii\db\Migration;

/**
 * Class m190711_161649_add_column_for_duplicated_records
 */
class m190711_161649_add_column_for_duplicated_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mas_single_send', 'duplicated_record', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mas_single_send', 'duplicated_record');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190711_161649_add_column_for_duplicated_records cannot be reverted.\n";

        return false;
    }
    */
}
