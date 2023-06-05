<?php

use yii\db\Migration;

/**
 * Class m210429_225050_add_has_sala_operativa_consumer
 */
class m210429_225050_add_has_sala_operativa_consumer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cap_consumer', 'sala_operativa', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cap_consumer', 'sala_operativa');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210429_225050_add_has_sala_operativa_consumer cannot be reverted.\n";

        return false;
    }
    */
}
