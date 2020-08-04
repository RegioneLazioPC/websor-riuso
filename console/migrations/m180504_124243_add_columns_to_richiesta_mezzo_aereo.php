<?php

use yii\db\Migration;

/**
 * Class m180504_124243_add_columns_to_richiesta_mezzo_aereo
 */
class m180504_124243_add_columns_to_richiesta_mezzo_aereo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_mezzo_aereo','idevento', $this->integer(11));
        $this->addColumn('richiesta_mezzo_aereo','idingaggio', $this->integer(11));
        $this->addColumn('richiesta_mezzo_aereo','idoperatore', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('richiesta_mezzo_aereo','idevento');
        $this->dropColumn('richiesta_mezzo_aereo','idingaggio');
        $this->dropColumn('richiesta_mezzo_aereo','idoperatore');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_124243_add_columns_to_richiesta_mezzo_aereo cannot be reverted.\n";

        return false;
    }
    */
}
