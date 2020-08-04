<?php

use yii\db\Migration;

/**
 * Class m180605_101347_add_icon_url_tipo_evento
 */
class m180605_101347_add_icon_url_tipo_evento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_tipologia', 'icon_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_tipologia', 'icon_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180605_101347_add_icon_url_tipo_evento cannot be reverted.\n";

        return false;
    }
    */
}
