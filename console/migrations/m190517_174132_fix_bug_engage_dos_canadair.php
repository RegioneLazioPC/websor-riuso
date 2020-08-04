<?php

use yii\db\Migration;

/**
 * Class m190517_174132_fix_bug_engage_dos_canadair
 */
class m190517_174132_fix_bug_engage_dos_canadair extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_elicottero', 'edited', $this->integer(1)->defaultValue(0));
        $this->addColumn('richiesta_canadair', 'edited', $this->integer(1)->defaultValue(0));
        $this->addColumn('richiesta_dos', 'edited', $this->integer(1)->defaultValue(0));

        /**
         * Le imposto tutte a 1
         */
        Yii::$app->db->createCommand("UPDATE richiesta_elicottero SET edited = 1")->execute();
        Yii::$app->db->createCommand("UPDATE richiesta_canadair SET edited = 1")->execute();
        Yii::$app->db->createCommand("UPDATE richiesta_dos SET edited = 1")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('richiesta_elicottero', 'edited');
        $this->dropColumn('richiesta_canadair', 'edited');
        $this->dropColumn('richiesta_dos', 'edited');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_174132_fix_bug_engage_dos_canadair cannot be reverted.\n";

        return false;
    }
    */
}
