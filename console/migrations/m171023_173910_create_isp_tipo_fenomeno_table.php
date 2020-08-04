<?php

use yii\db\Migration;

/**
 * Handles the creation of table `isp_tipo_fenomeno`.
 */
class m171023_173910_create_isp_tipo_fenomeno_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('isp_tipo_fenomeno');

        if ($tableSchema === null) {
            $this->createTable('isp_tipo_fenomeno', [
                'id' => $this->primaryKey(),
                'idparent' => $this->integer(11),
                'order' => $this->integer(5),
                'voce' => $this->string(255)
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('isp_tipo_fenomeno');
    }
}
