<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m211223_110614_add_tables_to_manage_app_update
 */
class m211223_110614_add_tables_to_manage_app_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        // il rl non ha effettuato il check sull'app
        $this->addColumn('utl_ingaggio', 'rl_to_check', $this->integer()->defaultValue(1));
        $this->addColumn('utl_ingaggio', 'checked_by_rl', $this->integer()->defaultValue(0));
        $this->addColumn('utl_ingaggio', 'checked_by_rl_at', $this->datetime());
        
        // rl da feedback, la sala deve vederlo
        $this->addColumn('utl_ingaggio', 'rl_feedback_to_check', $this->integer()->defaultValue(0));
        $this->addColumn('utl_ingaggio', 'feedback_by_rl', $this->integer()->defaultValue(0));
        $this->addColumn('utl_ingaggio', 'feedback_by_rl_at', $this->datetime());

        

        $this->createTable('utl_ingaggio_rl_feedback', [
            'id' => $this->primaryKey(),
            'id_ingaggio' => $this->integer(),
            'rl_codfiscale' => $this->string(16),
            'num_elenco_territoriale' => $this->integer(),
            'stato' => $this->integer(),
            'motivazione_rifiuto' => $this->integer(),
            'risorsa' => $this->json(),
            'volontari' => $this->json(),
            'note' => $this->string(2000),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ]);

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        // il rl non ha effettuato il check sull'app
        $this->dropColumn('utl_ingaggio', 'rl_to_check');
        $this->dropColumn('utl_ingaggio', 'checked_by_rl');
        $this->dropColumn('utl_ingaggio', 'checked_by_rl_at');
        
        // rl da feedback, la sala deve vederlo
        $this->dropColumn('utl_ingaggio', 'rl_feedback_to_check');
        $this->dropColumn('utl_ingaggio', 'feedback_by_rl');
        $this->dropColumn('utl_ingaggio', 'feedback_by_rl_at');

        
        $this->dropTable('utl_ingaggio_rl_feedback');

        
    }

}
