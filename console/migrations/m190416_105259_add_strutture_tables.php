<?php

use yii\db\Migration;

/**
 * Class m190416_105259_add_strutture_tables
 */
class m190416_105259_add_strutture_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('str_struttura', [
            'id' => $this->primaryKey(),
            'id_tipo_struttura' => $this->integer(),
            'id_ana_referente' => $this->integer(),
            'cf_rappresentante_legale' => $this->string(16),
            'cf_referente' => $this->string(16),
            'denominazione' => $this->string(255),
            'codicefiscale' => $this->string(16), // ??? in altri punti è codfiscale
            'partita_iva' => $this->string(16),
            'id_sync' => $this->string()
        ]);

        $this->createTable('str_tipo_struttura', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(),
            'id_sync' => $this->string()
        ]);


        $this->createTable('con_struttura_contatto', [
            'id' => $this->primaryKey(),
            'id_struttura' => $this->integer(),
            'id_contatto' => $this->integer(),
        ]);

        $this->createTable('str_struttura_sede', [
            'id' => $this->primaryKey(),
            'id_struttura' => $this->integer(),
            'indirizzo' => $this->string(),
            'id_comune' => $this->integer(),
            'tipo' => $this->integer(1), // 0=>sede legale, 1=>sede operativa
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5),
            'coord_x' => $this->float(),
            'coord_y' => $this->float(),
            'id_sync' => $this->string()
        ]);

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_sede_struttura_id_sync ON str_struttura_sede (id_sync);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_struttura_id_sync ON str_struttura (id_sync);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_tipo_struttura_id_sync ON str_tipo_struttura (id_sync);
            ")->execute();

        $this->createTable('con_struttura_sede_contatto', [
            'id' => $this->primaryKey(),
            'id_struttura_sede' => $this->integer(),
            'id_contatto' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-con_struttura_sede_contatto_sede',
            'con_struttura_sede_contatto',
            'id_struttura_sede',
            'str_struttura_sede',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_struttura_sede_contatto_contatto',
            'con_struttura_sede_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-struttura_sede_comune',
            'str_struttura_sede',
            'id_comune',
            'loc_comune',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-struttura_tipo_tipo',
            'str_struttura',
            'id_tipo_struttura',
            'str_tipo_struttura',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-con_struttura_contatto_struttura',
            'con_struttura_contatto',
            'id_struttura',
            'str_struttura',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_struttura_contatto_contatto',
            'con_struttura_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-struttura_rappr_legale',
            'str_struttura',
            'cf_rappresentante_legale',
            'utl_anagrafica',
            'codfiscale',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-struttura_referente',
            'str_struttura',
            'cf_referente',
            'utl_anagrafica',
            'codfiscale',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-con_struttura_sede_contatto_sede',
            'con_struttura_sede_contatto'
        );

        $this->dropForeignKey(
            'fk-con_struttura_sede_contatto_contatto',
            'con_struttura_sede_contatto'
        );

        $this->dropForeignKey(
            'fk-struttura_sede_comune',
            'str_struttura_sede'
        );

        $this->dropForeignKey(
            'fk-struttura_tipo_tipo',
            'str_struttura'
        );

        $this->dropForeignKey(
            'fk-con_struttura_contatto_struttura',
            'con_struttura_contatto'
        );

        $this->dropForeignKey(
            'fk-con_struttura_contatto_contatto',
            'con_struttura_contatto'
        );

        $this->dropForeignKey(
            'fk-struttura_rappr_legale',
            'str_struttura'
        );

        $this->dropForeignKey(
            'fk-struttura_referente',
            'str_struttura'
        );

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_sede_struttura_id_sync
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_struttura_id_sync
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_tipo_struttura_id_sync
            ")->execute();

        $this->dropTable('str_struttura');

        $this->dropTable('str_tipo_struttura');

        $this->dropTable('con_struttura_contatto');

        $this->dropTable('str_struttura_sede');

        $this->dropTable('con_struttura_sede_contatto');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190416_105259_add_strutture_tables cannot be reverted.\n";

        return false;
    }
    */
}
