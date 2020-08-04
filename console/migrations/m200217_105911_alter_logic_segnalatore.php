<?php

use yii\db\Migration;

/**
 * Class m200217_105911_alter_logic_segnalatore
 */
class m200217_105911_alter_logic_segnalatore extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_segnalazione', 'nome_segnalatore', $this->string());
        $this->addColumn('utl_segnalazione', 'cognome_segnalatore', $this->string());
        $this->addColumn('utl_segnalazione', 'telefono_segnalatore', $this->string());
        $this->addColumn('utl_segnalazione', 'email_segnalatore', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_segnalazione', 'nome_segnalatore');
        $this->dropColumn('utl_segnalazione', 'cognome_segnalatore');
        $this->dropColumn('utl_segnalazione', 'telefono_segnalatore');
        $this->dropColumn('utl_segnalazione', 'email_segnalatore');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_105911_alter_logic_segnalatore cannot be reverted.\n";

        return false;
    }
    */
}
