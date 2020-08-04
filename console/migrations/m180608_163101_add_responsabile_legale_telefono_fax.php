<?php

use yii\db\Migration;

/**
 * Class m180608_163101_add_responsabile_legale_telefono_fax
 */
class m180608_163101_add_responsabile_legale_telefono_fax extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vol_organizzazione', 'tel_responsabile', $this->string());
        $this->addColumn('vol_organizzazione', 'pec_responsabile', $this->string());
        $this->addColumn('vol_organizzazione', 'email_responsabile', $this->string());
        $this->addColumn('vol_organizzazione', 'nome_responsabile', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_organizzazione', 'tel_responsabile');
        $this->dropColumn('vol_organizzazione', 'pec_responsabile');
        $this->dropColumn('vol_organizzazione', 'email_responsabile');
        $this->dropColumn('vol_organizzazione', 'nome_responsabile');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180608_163101_add_responsabile_legale_telefono_fax cannot be reverted.\n";

        return false;
    }
    */
}
