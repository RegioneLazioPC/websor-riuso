<?php

use yii\db\Migration;

/**
 * Class m180419_165727_add_engaged_column
 */
class m180419_165727_add_engaged_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS routing.view_ingaggio_organizzazioni")
            ->execute();
        $this->addColumn('utl_attrezzatura', 'engaged', $this->boolean()->defaultValue(false));
        $this->addColumn('utl_automezzo', 'engaged', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS routing.view_ingaggio_organizzazioni")
            ->execute();
        $this->dropColumn('utl_attrezzatura', 'engaged');
        $this->dropColumn('utl_automezzo', 'engaged');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180419_165727_add_engaged_column cannot be reverted.\n";

        return false;
    }
    */
}
