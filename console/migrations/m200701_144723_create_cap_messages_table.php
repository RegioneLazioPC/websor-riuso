<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cap_messages`.
 */
class m200701_144723_create_cap_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cap_messages', [
            'id' => $this->primaryKey(),
            'cap_feed_url' => $this->string(500),
            'url' => $this->string(500)->unique()->notNull(),
            'identifier' => $this->string(500)->unique()->notNull(),
            'ref_identifier' => $this->string(500),
            'type' => $this->string(),
            'xml_content' => 'xml',
            'json_content' => $this->json(),
            'date_creation' => 'timestamp with time zone default NOW()'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cap_messages');
    }
}
