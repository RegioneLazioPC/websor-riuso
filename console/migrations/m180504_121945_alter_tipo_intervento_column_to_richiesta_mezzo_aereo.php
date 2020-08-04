<?php

use yii\db\Migration;

/**
 * Class m180504_121945_alter_tipo_intervento_column_to_richiesta_mezzo_aereo
 */
class m180504_121945_alter_tipo_intervento_column_to_richiesta_mezzo_aereo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('richiesta_mezzo_aereo','tipo_intervento');
        $this->addColumn('richiesta_mezzo_aereo','tipo_intervento', $this->integer(11));

        $this->dropColumn('richiesta_mezzo_aereo','elettrodotto');
        $this->addColumn('richiesta_mezzo_aereo','elettrodotto', $this->integer(11));

        $this->dropColumn('richiesta_mezzo_aereo','oreografia');
        $this->addColumn('richiesta_mezzo_aereo','oreografia', $this->integer(11));

        $this->dropColumn('richiesta_mezzo_aereo','vento');
        $this->addColumn('richiesta_mezzo_aereo','vento', $this->integer(11));

        $this->dropColumn('richiesta_mezzo_aereo','ostacoli');
        $this->addColumn('richiesta_mezzo_aereo','ostacoli', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //$this->dropColumn('richiesta_mezzo_aereo','tipo_intervento');
        //$this->dropColumn('richiesta_mezzo_aereo','elettrodotto');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_121945_alter_tipo_intervento_column_to_richiesta_mezzo_aereo cannot be reverted.\n";

        return false;
    }
    */
}
