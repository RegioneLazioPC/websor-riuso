<?php

use yii\db\Migration;

/**
 * Class m191209_110841_alter_utl_utente_add_id_organizzazione
 */
class m191209_110841_alter_utl_utente_add_id_organizzazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_utente', 'id_organizzazione', $this->integer());
        $this->addForeignKey(
            'fk-utl_utente_organizzazione',
            'utl_utente',
            'id_organizzazione',
            'vol_organizzazione', 
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-utl_utente_organizzazione',
            'utl_utente'
        );
        $this->dropColumn('utl_utente', 'id_organizzazione');
    }

}
