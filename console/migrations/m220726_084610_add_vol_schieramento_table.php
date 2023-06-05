<?php

use yii\db\Migration;

/**
 * Class m220726_084610_add_vol_schieramento_table
 */
class m220726_084610_add_vol_schieramento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('vol_schieramento', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(1000)->unique(),
            'data_validita' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ]);

        $this->createTable('con_mezzo_schieramento', [
            'id' => $this->primaryKey(),
            'id_utl_automezzo' => $this->integer(),
            'id_vol_schieramento' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ]);

        $this->createTable('con_attrezzatura_schieramento', [
            'id' => $this->primaryKey(),
            'id_utl_attrezzatura' => $this->integer(),
            'id_vol_schieramento' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ]);  

        $this->addColumn('utl_ingaggio', 'deviato', $this->boolean()->defaultValue(false));

        $this->addForeignKey(
            'fk-schieramento_mezzo_mezzo',
            'con_mezzo_schieramento',
            'id_utl_automezzo',
            'utl_automezzo', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-schieramento_mezzo_schieramento',
            'con_mezzo_schieramento',
            'id_vol_schieramento',
            'vol_schieramento', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-schieramento_attrezzatura_attrezzatura',
            'con_attrezzatura_schieramento',
            'id_utl_attrezzatura',
            'utl_attrezzatura', 
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-schieramento_attrezzatura_schieramento',
            'con_attrezzatura_schieramento',
            'id_vol_schieramento',
            'vol_schieramento', 
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
            'fk-schieramento_mezzo_mezzo',
            'con_mezzo_schieramento'
        );

        $this->dropForeignKey(
            'fk-schieramento_mezzo_schieramento',
            'con_mezzo_schieramento'
        );

        $this->dropForeignKey(
            'fk-schieramento_attrezzatura_attrezzatura',
            'con_attrezzatura_schieramento'
        );

        $this->dropForeignKey(
            'fk-schieramento_attrezzatura_schieramento',
            'con_attrezzatura_schieramento'
        );

        $this->dropTable('vol_schieramento');

        $this->dropTable('con_mezzo_schieramento');

        $this->dropTable('con_attrezzatura_schieramento');  

        $this->dropColumn('utl_ingaggio', 'deviato');
    }

}
