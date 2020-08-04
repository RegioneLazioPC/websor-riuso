<?php

use yii\db\Migration;

/**
 * Class m180404_150144_create_tables_for_pclazione_63_72
 */
class m180404_150144_create_tables_for_pclazione_63_72 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('vol_organizzazione_utente', [
            'id' => $this->primaryKey(),
            'idutente' => $this->integer(),
            'idorganizzazione' => $this->integer(),
            'idsede' => $this->integer(),
            'idtipo' => $this->integer()
        ]);

        $this->createTable('utl_utente_tipo', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(300)
        ]);

        $this->addForeignKey(
            'fk_vol_organizzazione_utente_utente',
            'vol_organizzazione_utente',
            'idutente',
            'utl_utente',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_vol_organizzazione_utente_organizzazione',
            'vol_organizzazione_utente',
            'idorganizzazione',
            'vol_organizzazione',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_vol_organizzazione_utente_sede',
            'vol_organizzazione_utente',
            'idsede',
            'vol_sede',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_vol_organizzazione_utente_tipo_utente',
            'vol_organizzazione_utente',
            'idtipo',
            'utl_utente_tipo',
            'id',
            'SET NULL'
        );


        $this->addColumn('vol_sede', 'disponibilita_oraria', 'json');
        $this->addColumn('vol_sede', 'lat', $this->double(11,5)); //'geography(POINT)'
        $this->addColumn('vol_sede', 'lon', $this->double(11,5));


        $this->createTable('utl_automezzo_tipo', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(300)
        ]);

        $this->createTable('utl_attrezzatura_tipo', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(300)
        ]);

        $this->createTable('utl_categoria_automezzo_attrezzatura', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(300)
        ]);

        $this->addColumn('utl_automezzo', 'idcategoria', $this->integer());
        $this->addColumn('utl_automezzo', 'idtipo', $this->integer());
        $this->addColumn('utl_automezzo', 'capacita', $this->float());
        $this->addColumn('utl_automezzo', 'disponibilita', $this->string());
        $this->addColumn('utl_automezzo', 'idorganizzazione', $this->integer());
        $this->addColumn('utl_automezzo', 'idsede', $this->integer());

        $this->addForeignKey(
            'fk_utl_automezzo_categoria',
            'utl_automezzo',
            'idcategoria',
            'utl_categoria_automezzo_attrezzatura',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_automezzo_tipo',
            'utl_automezzo',
            'idtipo',
            'utl_automezzo_tipo',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_automezzo_organizzazione',
            'utl_automezzo',
            'idorganizzazione',
            'vol_organizzazione',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_automezzo_sede',
            'utl_automezzo',
            'idsede',
            'vol_sede',
            'id',
            'SET NULL'
        );

        $this->createTable('utl_attrezzatura', [
            'id' => $this->primaryKey(),
            'idcategoria' => $this->integer(),
            'idtipo' => $this->integer(),
            'classe' => $this->string(100),
            'sottoclasse' => $this->string(100),
            'modello' => $this->string(100),
            'capacita' => $this->float(),
            'unita' => $this->string(255),
            'idorganizzazione' => $this->integer(),
            'idsede' => $this->integer(),
            'idautomezzo' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk_utl_attrezzatura_categoria',
            'utl_attrezzatura',
            'idcategoria',
            'utl_categoria_automezzo_attrezzatura',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_attrezzatura_tipo',
            'utl_attrezzatura',
            'idtipo',
            'utl_attrezzatura_tipo',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_attrezzatura_organizzazione',
            'utl_attrezzatura',
            'idorganizzazione',
            'vol_organizzazione',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_attrezzatura_sede',
            'utl_attrezzatura',
            'idsede',
            'vol_sede',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_utl_attrezzatura_automezzo',
            'utl_attrezzatura',
            'idautomezzo',
            'utl_automezzo',
            'id',
            'SET NULL'
        );




    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_utl_attrezzatura_categoria',
            'utl_attrezzatura');

        $this->dropForeignKey(
            'fk_utl_attrezzatura_tipo',
            'utl_attrezzatura');

        $this->dropForeignKey(
            'fk_utl_attrezzatura_organizzazione',
            'utl_attrezzatura');

        $this->dropForeignKey(
            'fk_utl_attrezzatura_sede',
            'utl_attrezzatura');

        $this->dropForeignKey(
            'fk_utl_attrezzatura_automezzo',
            'utl_attrezzatura');

        $this->dropForeignKey(
            'fk_utl_automezzo_categoria',
            'utl_automezzo');

        $this->dropForeignKey(
            'fk_utl_automezzo_tipo',
            'utl_automezzo');

        $this->dropForeignKey(
            'fk_utl_automezzo_organizzazione',
            'utl_automezzo');

        $this->dropForeignKey(
            'fk_utl_automezzo_sede',
            'utl_automezzo');

        $this->dropColumn('utl_automezzo', 'idcategoria' );
        $this->dropColumn('utl_automezzo', 'idtipo' );
        $this->dropColumn('utl_automezzo', 'capacita' );
        $this->dropColumn('utl_automezzo', 'disponibilita' );
        $this->dropColumn('utl_automezzo', 'idorganizzazione' );
        $this->dropColumn('utl_automezzo', 'idsede' );

        $this->dropTable('utl_automezzo_tipo');
        $this->dropTable('utl_attrezzatura_tipo');
        $this->dropTable('utl_categoria_automezzo_attrezzatura');

        $this->dropColumn('vol_sede', 'disponibilita_oraria');
        $this->dropColumn('vol_sede', 'lat'); //'geography(POINT)'
        $this->dropColumn('vol_sede', 'lon');



        $this->dropForeignKey(
            'fk_vol_organizzazione_utente_utente',
            'vol_organizzazione_utente');

        $this->dropForeignKey(
            'fk_vol_organizzazione_utente_organizzazione',
            'vol_organizzazione_utente');

        $this->dropForeignKey(
            'fk_vol_organizzazione_utente_sede',
            'vol_organizzazione_utente');

        $this->dropTable('vol_organizzazione_utente');
        $this->dropTable('utl_utente_tipo');

        $this->dropTable('utl_attrezzatura');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180404_150144_create_tables_for_pclazione_63_72 cannot be reverted.\n";

        return false;
    }
    */
}
