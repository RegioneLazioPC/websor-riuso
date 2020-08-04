<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181206_164515_add_custom_rubrica
 */
class m181206_164515_add_custom_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mas_rubrica', [
            'id' => $this->primaryKey(),
            'dettagli' => $this->text(),
            'ruolo' => $this->string(),
            'id_anagrafica' => $this->integer(),
            'id_indirizzo' => $this->integer(),
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->addForeignKey(
            'fk-mas_rubrica_anagrafica_ana',
            'mas_rubrica',
            'id_anagrafica',
            'utl_anagrafica',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mas_rubrica_indirizzo',
            'mas_rubrica',
            'id_indirizzo',
            'utl_indirizzo',
            'id',
            'SET NULL'
        );

        Yii::$app->db->createCommand("ALTER TABLE mas_rubrica ADD COLUMN geom geometry(Point, 4326)")
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey(
            'fk-mas_rubrica_anagrafica_ana',
            'mas_rubrica'
        );

        $this->dropForeignKey(
            'fk-mas_rubrica_indirizzo',
            'mas_rubrica'
        );

        $this->dropTable('mas_rubrica');

        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181206_164515_add_custom_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
