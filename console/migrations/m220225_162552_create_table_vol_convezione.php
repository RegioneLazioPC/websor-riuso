<?php

use yii\db\Migration;

/**
 * Class m220225_162552_create_table_vol_convezione
 */
class m220225_162552_create_table_vol_convezione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('vol_convenzione', [
            'id' => $this->primaryKey(),
            'id_organizzazione' => $this->integer(11)->notNull(),
            'id_ref' => $this->integer(11)->notNull(),
            'num_riferimento' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_convezione_organizzazione', 'vol_convenzione', 'id_organizzazione', 'vol_organizzazione', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_convezione_organizzazione', 'vol_convenzione');
        $this->dropTable('vol_convenzione');
    }
}
