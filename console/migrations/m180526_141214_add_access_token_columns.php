<?php

use yii\db\Migration;

/**
 * Class m180526_141214_add_access_token_columns
 */
class m180526_141214_add_access_token_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'access_token', $this->string());
        $this->addColumn('user', 'user_agent', $this->string());
        $this->addColumn('user', 'ip_address', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'access_token');
        $this->dropColumn('user', 'ip_address');
        $this->dropColumn('user', 'user_agent');
    }
}
