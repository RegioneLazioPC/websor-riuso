<?php

use yii\db\Migration;

/**
 * Class m220228_155609_sala_operativa_esterna
 */
class m220228_155609_sala_operativa_esterna extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('sala_operativa_esterna', [
            'id' => $this->primaryKey(),
            'nome' => $this->string()->notNull(),
            'url_endpoint' => $this->string()->notNull(),
            'api_auth_url' => $this->string(),
            'api_username' => $this->string(),
            'api_password' => $this->string(),
        ]);

        $this->createTable('con_evento_sala_esterna', [
            'id' => $this->primaryKey(),
            'id_evento' => $this->integer(11),
            'id_sala_op_esterna' => $this->integer(11)
        ]);

        $this->addForeignKey('fk_soext_evento', 'con_evento_sala_esterna', 'id_evento', 'utl_evento', 'id', 'CASCADE');
        $this->addForeignKey('fk_soext_sala', 'con_evento_sala_esterna', 'id_sala_op_esterna', 'sala_operativa_esterna', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_soext_sala', 'con_evento_sala_esterna');
        $this->dropForeignKey('fk_soext_evento', 'con_evento_sala_esterna');
        $this->dropTable('con_evento_sala_esterna');
        $this->dropTable('sala_operativa_esterna');
    }
}
