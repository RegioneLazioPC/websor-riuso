<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tbl_tipo_risorsa`.
 */
class m191206_092249_create_tbl_tipo_risorsa_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tbl_tipo_risorsa_meta', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(1),
            'show_in_column' => $this->integer(1)->defaultValue(0),
            'extra' => $this->string(),
            'key' => $this->string(),
            'ref_id' => $this->string(),
            'label' => $this->string(),
            'id_sync' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ]);

        $this->addColumn('utl_automezzo', 'meta', $this->json());
        $this->addColumn('utl_attrezzatura', 'meta', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_automezzo', 'meta');
        $this->dropColumn('utl_attrezzatura', 'meta');
        $this->dropTable('tbl_tipo_risorsa_meta');
    }
}
