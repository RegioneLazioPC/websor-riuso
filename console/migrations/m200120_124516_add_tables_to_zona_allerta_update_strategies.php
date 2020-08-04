<?php

use yii\db\Migration;

/**
 * Class m200120_124516_add_tables_to_zona_allerta_update_strategies
 */
class m200120_124516_add_tables_to_zona_allerta_update_strategies extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vol_tipo_organizzazione', 'update_zona_allerta_strategy', $this->integer(1)->defaultValue(0));
        $this->addColumn('ent_tipo_ente', 'update_zona_allerta_strategy', $this->integer(1)->defaultValue(0));
        $this->addColumn('str_tipo_struttura', 'update_zona_allerta_strategy', $this->integer(1)->defaultValue(0));

        $this->addColumn('vol_organizzazione', 'update_zona_allerta_strategy', $this->integer(1)->defaultValue(0));
        $this->addColumn('ent_ente', 'update_zona_allerta_strategy', $this->integer(1)->defaultValue(0));
        $this->addColumn('str_struttura', 'update_zona_allerta_strategy', $this->integer(1)->defaultValue(0));

        $this->addColumn('vol_organizzazione', 'zone_allerta', $this->string()->defaultValue(""));
        $this->addColumn('ent_ente', 'zone_allerta', $this->string()->defaultValue(""));
        $this->addColumn('str_struttura', 'zone_allerta', $this->string()->defaultValue(""));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vol_tipo_organizzazione', 'update_zona_allerta_strategy');
        $this->dropColumn('ent_tipo_ente', 'update_zona_allerta_strategy');
        $this->dropColumn('str_tipo_struttura', 'update_zona_allerta_strategy');

        $this->dropColumn('vol_organizzazione', 'update_zona_allerta_strategy');
        $this->dropColumn('ent_ente', 'update_zona_allerta_strategy');
        $this->dropColumn('str_struttura', 'update_zona_allerta_strategy');

        $this->dropColumn('vol_organizzazione', 'zone_allerta');
        $this->dropColumn('ent_ente', 'zone_allerta');
        $this->dropColumn('str_struttura', 'zone_allerta');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200120_124516_add_tables_to_zona_allerta_update_strategies cannot be reverted.\n";

        return false;
    }
    */
}
