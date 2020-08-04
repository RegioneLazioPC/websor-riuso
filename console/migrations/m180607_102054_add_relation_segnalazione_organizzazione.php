<?php

use yii\db\Migration;

/**
 * Class m180607_102054_add_relation_segnalazione_organizzazione
 */
class m180607_102054_add_relation_segnalazione_organizzazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_segnalazione', 'id_organizzazione', $this->integer());
        $this->addForeignKey(
            'fk_segnalazione_organizzazione',
            'utl_segnalazione',
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
            'fk_segnalazione_organizzazione',
            'utl_segnalazione'
        );

        $this->dropColumn('utl_segnalazione', 'id_organizzazione');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_102054_add_relation_segnalazione_organizzazione cannot be reverted.\n";

        return false;
    }
    */
}
