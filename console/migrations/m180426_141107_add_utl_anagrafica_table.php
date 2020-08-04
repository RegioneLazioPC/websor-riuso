<?php

use yii\db\Migration;

/**
 * Class m180426_141107_add_utl_anagrafica_table
 */
class m180426_141107_add_utl_anagrafica_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('utl_anagrafica', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(255),
            'cognome' => $this->string(255),
            'codfiscale' => $this->string(16),
            'telefono' => $this->string(20),
            'email' => $this->string(355),
            'data_nascita' => $this->date(),
            'luogo_nascita' => $this->integer(),
            'comune_residenza' => $this->integer(),
        ]);

        $this->dropColumn('utl_operatore_pc', 'username');
        $this->dropColumn('utl_operatore_pc', 'password');
        $this->dropColumn('utl_operatore_pc', 'nome');
        $this->dropColumn('utl_operatore_pc', 'cognome');
        $this->dropColumn('utl_operatore_pc', 'email');

        $this->dropColumn('utl_utente', 'nome');
        $this->dropColumn('utl_utente', 'cognome');
        $this->dropColumn('utl_utente', 'email');
        $this->dropColumn('utl_utente', 'telefono');
        $this->dropColumn('utl_utente', 'codfiscale');
        $this->dropColumn('utl_utente', 'luogo_nascita');
        $this->dropColumn('utl_utente', 'comune_residenza');

        $this->addForeignKey(
            'fk-vol_volontario_anagrafica',
            'vol_volontario',
            'id_anagrafica',
            'utl_anagrafica',
            'id'
        );

        $this->addColumn('utl_operatore_pc', 'id_anagrafica', $this->integer() );
        $this->addForeignKey(
            'fk-utl_operatore_pc_anagrafica',
            'utl_operatore_pc',
            'id_anagrafica',
            'utl_anagrafica',
            'id'
        );

        $this->addColumn('utl_utente', 'id_anagrafica', $this->integer() );
        $this->addForeignKey(
            'fk-utl_utente_anagrafica',
            'utl_utente',
            'id_anagrafica',
            'utl_anagrafica',
            'id'
        );

        $this->addForeignKey(
            'fk-vol_volontario_comune_nascita',
            'utl_anagrafica',
            'luogo_nascita',
            'loc_comune',
            'id'
        );

        $this->addForeignKey(
            'fk-vol_volontario_comune_residenza',
            'utl_anagrafica',
            'comune_residenza',
            'loc_comune',
            'id'
        );

        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropForeignKey(
            'fk-vol_volontario_comune_nascita',
            'utl_anagrafica'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_comune_residenza',
            'utl_anagrafica'
        );

        $this->dropForeignKey(
            'fk-vol_volontario_anagrafica',
            'vol_volontario'
        );

        $this->dropForeignKey(
            'fk-utl_operatore_pc_anagrafica',
            'utl_operatore_pc'
        );

        $this->dropForeignKey(
            'fk-utl_utente_anagrafica',
            'utl_utente'
        );

        $this->dropColumn('utl_operatore_pc', 'id_anagrafica' );
        $this->dropColumn('utl_utente', 'id_anagrafica' );

        $this->addColumn('utl_operatore_pc', 'username', $this->string(255) );
        $this->addColumn('utl_operatore_pc', 'password', $this->string(255) );
        $this->addColumn('utl_operatore_pc', 'nome', $this->string(255) );
        $this->addColumn('utl_operatore_pc', 'cognome', $this->string(255) );
        $this->addColumn('utl_operatore_pc', 'email', $this->string(255) );

        $this->addColumn('utl_utente', 'nome', $this->string(255) );
        $this->addColumn('utl_utente', 'cognome', $this->string(255) );
        $this->addColumn('utl_utente', 'email', $this->string(255) );
        $this->addColumn('utl_utente', 'telefono', $this->string(255) );
        $this->addColumn('utl_utente', 'codfiscale', $this->string(255) );
        $this->addColumn('utl_utente', 'luogo_nascita', $this->string(255) );
        $this->addColumn('utl_utente', 'comune_residenza', $this->string(255) );

        $this->dropTable( 'utl_anagrafica' );

    }

   
}
