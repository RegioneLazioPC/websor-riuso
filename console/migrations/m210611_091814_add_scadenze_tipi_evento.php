<?php

use yii\db\Migration;

/**
 * Class m210611_091814_add_scadenze_tipi_evento
 */
class m210611_091814_add_scadenze_tipi_evento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_tipologia', 'valido_dal', $this->date());
        $this->addColumn('utl_tipologia', 'valido_al', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_tipologia', 'valido_dal');
        $this->dropColumn('utl_tipologia', 'valido_al');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210611_091814_add_scadenze_tipi_evento cannot be reverted.\n";

        return false;
    }
    */
}
