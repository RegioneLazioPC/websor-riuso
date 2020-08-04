<?php

use yii\db\Migration;

/**
 * Class m180417_171853_add_column_zona_altimetrica
 */
class m180417_171853_add_column_zona_altimetrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('loc_comune', 'zona_altimetrica', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('loc_comune', 'zona_altimetrica');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180417_171853_add_column_zona_altimetrica cannot be reverted.\n";

        return false;
    }
    */
}
