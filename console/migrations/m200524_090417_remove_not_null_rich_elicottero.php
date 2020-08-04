<?php

use yii\db\Migration;

/**
 * Class m200524_090417_remove_not_null_rich_elicottero
 */
class m200524_090417_remove_not_null_rich_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('richiesta_elicottero', 'priorita_intervento', 'DROP NOT NULL'); 
        $this->alterColumn('richiesta_elicottero', 'tipo_vegetazione', 'DROP NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('richiesta_elicottero', 'priorita_intervento', 'SET DEFAULT NULL'); 
        $this->alterColumn('richiesta_elicottero', 'tipo_vegetazione', 'SET DEFAULT NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200524_090417_remove_not_null_rich_elicottero cannot be reverted.\n";

        return false;
    }
    */
}
