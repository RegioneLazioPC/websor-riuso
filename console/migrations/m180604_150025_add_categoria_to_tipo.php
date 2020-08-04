<?php

use yii\db\Migration;

/**
 * Class m180604_150025_add_categoria_to_tipo
 */
class m180604_150025_add_categoria_to_tipo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_aggregatore_tipologie', 'id_categoria', $this->integer());
        $this->addColumn('utl_categoria_automezzo_attrezzatura', 'id_tipo_evento', $this->integer());

        $this->addForeignKey(
            'fk_aggregatore_tipologie_categoria',
            'utl_aggregatore_tipologie',
            'id_categoria',
            'utl_categoria_automezzo_attrezzatura',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_categoria_automezzo_attrezzatura_evento',
            'utl_categoria_automezzo_attrezzatura',
            'id_tipo_evento',
            'utl_tipologia',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_aggregatore_tipologie_categoria',
            'utl_aggregatore_tipologie'
        );

        $this->dropForeignKey(
            'fk_categoria_automezzo_attrezzatura_evento',
            'utl_categoria_automezzo_attrezzatura'
        );

        $this->dropColumn('utl_aggregatore_tipologie', 'id_categoria');
        $this->dropColumn('utl_categoria_automezzo_attrezzatura', 'id_evento');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180604_150025_add_categoria_to_tipo cannot be reverted.\n";

        return false;
    }
    */
}
