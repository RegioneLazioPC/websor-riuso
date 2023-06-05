<?php

use yii\db\Migration;

/**
 * Class m210826_154709_alter_length_cap_messages_varchar
 */
class m210826_154709_alter_length_cap_messages_varchar extends Migration
{
    private $cols = [
            'identifier',
            'sender',
            'source',
            'scope',
            'restriction',
            'addresses',
            'incidents',
            'references',
            'event',
            'audience',
            'headline',
            'senderName',
            'web',
            'contact'
        ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        foreach ($this->cols as $col) {
            Yii::$app->db->createCommand("ALTER TABLE cap_exposed_message ALTER COLUMN \"".$col."\" TYPE VARCHAR(5000)")->execute();
        }
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->cols as $col) {
            Yii::$app->db->createCommand("ALTER TABLE cap_exposed_message ALTER COLUMN \"".$col."\" TYPE VARCHAR(255)")->execute();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210826_154709_alter_length_cap_messages_varchar cannot be reverted.\n";

        return false;
    }
    */
}
