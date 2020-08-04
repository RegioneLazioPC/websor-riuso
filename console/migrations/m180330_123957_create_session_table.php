<?php

use yii\db\Migration;

/**
 * Handles the creation of table `session`.
 */
class m180330_123957_create_session_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('session', [
            'id' => $this->string(255),
            'expire' => $this->integer(),
            'data' => $this->text(),
            'id_user' => $this->integer(),
            'last_write' => $this->timestamp(),
            'PRIMARY KEY(id)'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('session');
    }
}
