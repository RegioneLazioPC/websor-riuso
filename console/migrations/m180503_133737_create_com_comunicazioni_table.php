<?php

use yii\db\Migration;

/**
 * Handles the creation of table `com_comunicazioni`.
 */
class m180503_133737_create_com_comunicazioni_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('com_comunicazioni', [
            'id' => $this->primaryKey(),
            'tipo' => $this->integer(), // 1 -> 'Email', 2 => 'Sms', 3 => 'Fax', 4 => 'Telefono'
            'oggetto' => $this->string(255),
            'contenuto' => $this->text(),
            'contatto' => $this->string(255),
            'created_at' => $this->timestamp()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('com_comunicazioni');
    }
}
