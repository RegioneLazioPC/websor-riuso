<?php

use yii\db\Migration;

/**
 * Class m190416_102534_add_enti_tables
 */
class m190416_102534_add_enti_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_anagrafica_cf ON utl_anagrafica (codfiscale);
            ")->execute();

        $this->createTable('ent_ente', [
            'id' => $this->primaryKey(),
            'id_tipo_ente' => $this->integer(),
            'id_ana_referente' => $this->integer(),
            'cf_rappresentante_legale' => $this->string(16),
            'cf_referente' => $this->string(16),
            'denominazione' => $this->string(255),
            'codicefiscale' => $this->string(16), // ??? in altri punti è codfiscale
            'partita_iva' => $this->string(16),
            'id_sync' => $this->string()
        ]);

        $this->createTable('ent_tipo_ente', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(),
            'id_sync' => $this->string()
        ]);


        $this->createTable('con_ente_contatto', [
            'id' => $this->primaryKey(),
            'id_ente' => $this->integer(),
            'id_contatto' => $this->integer(),
        ]);

        $this->createTable('ent_ente_sede', [
            'id' => $this->primaryKey(),
            'id_ente' => $this->integer(),
            'indirizzo' => $this->string(),
            'id_comune' => $this->integer(),
            'tipo' => $this->integer(1), // 0=>sede legale, 1=>sede operativa
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5),
            'coord_x' => $this->float(),
            'coord_y' => $this->float(),
            'id_sync' => $this->string()
        ]);

        $this->createTable('con_ente_sede_contatto', [
            'id' => $this->primaryKey(),
            'id_ente_sede' => $this->integer(),
            'id_contatto' => $this->integer(),
        ]);

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_sede_ente_id_sync ON ent_ente_sede (id_sync);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_ente_id_sync ON ent_ente (id_sync);
            ")->execute();

        Yii::$app->db->createCommand("
            CREATE UNIQUE INDEX idx_unique_tipo_ente_id_sync ON ent_tipo_ente (id_sync);
            ")->execute();

        $this->addForeignKey(
            'fk-con_ente_sede_contatto_sede',
            'con_ente_sede_contatto',
            'id_ente_sede',
            'ent_ente_sede',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_ente_sede_contatto_contatto',
            'con_ente_sede_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ente_sede_ente',
            'ent_ente_sede',
            'id_ente',
            'ent_ente',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ente_sede_comune',
            'ent_ente_sede',
            'id_comune',
            'loc_comune',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ente_tipo_tipo',
            'ent_ente',
            'id_tipo_ente',
            'ent_tipo_ente',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-con_ente_contatto_ente',
            'con_ente_contatto',
            'id_ente',
            'ent_ente',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_ente_contatto_contatto',
            'con_ente_contatto',
            'id_contatto',
            'utl_contatto',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ente_rappr_legale',
            'ent_ente',
            'cf_rappresentante_legale',
            'utl_anagrafica',
            'codfiscale',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-ente_referente',
            'ent_ente',
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
            'fk-con_ente_sede_contatto_sede',
            'con_ente_sede_contatto'
        );

        $this->dropForeignKey(
            'fk-con_ente_sede_contatto_contatto',
            'con_ente_sede_contatto'
        );

        $this->dropForeignKey(
            'fk-ente_sede_ente',
            'ent_ente_sede'
        );

        $this->dropForeignKey(
            'fk-ente_sede_comune',
            'ent_ente_sede'
        );

        $this->dropForeignKey(
            'fk-ente_rappr_legale',
            'ent_ente'
        );

        $this->dropForeignKey(
            'fk-ente_referente',
            'ent_ente'
        );

        $this->dropForeignKey(
            'fk-ente_tipo_tipo',
            'ent_ente'
        );

        $this->dropForeignKey(
            'fk-con_ente_contatto_ente',
            'con_ente_contatto'
        );

        $this->dropForeignKey(
            'fk-con_ente_contatto_contatto',
            'con_ente_contatto'
        );

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_anagrafica_cf
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_sede_ente_id_sync
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_ente_id_sync
            ")->execute();

        Yii::$app->db->createCommand("
            DROP INDEX idx_unique_tipo_ente_id_sync
            ")->execute();

        $this->dropTable('con_ente_sede_contatto');

        $this->dropTable('ent_ente');

        $this->dropTable('ent_ente_sede');

        $this->dropTable('ent_tipo_ente');

        $this->dropTable('con_ente_contatto');
    }

    
}
