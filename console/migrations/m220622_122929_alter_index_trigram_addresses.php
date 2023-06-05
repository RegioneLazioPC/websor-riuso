<?php

use yii\db\Migration;

/**
 * Class m220622_122929_alter_index_trigram_addresses
 */
class m220622_122929_alter_index_trigram_addresses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            Yii::$app->db->createCommand("CREATE INDEX addresses_trgm_index ON _autocomplete_addresses USING gist (full_address gist_trgm_ops);")->execute();
        } catch(\Exception $e) {
            throw new \Exception("Verifica l'installazione dell'estensione pg_trgm", 1);
            
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP INDEX addresses_trgm_index;")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_122929_alter_index_trigram_addresses cannot be reverted.\n";

        return false;
    }
    */
}
