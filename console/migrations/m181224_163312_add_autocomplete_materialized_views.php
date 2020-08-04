<?php

use yii\db\Migration;

/**
 * Class m181224_163312_add_autocomplete_materialized_views
 */
class m181224_163312_add_autocomplete_materialized_views extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW materialized_view_autocomplete_addresses
            AS
            SELECT CONCAT( loc_indirizzo.name, ' ', loc_civico.civico, ', ', loc_civico.cap, ', ', loc_comune.comune, ', (', loc_provincia.sigla, ')' ) as address, loc_civico.lat, loc_civico.lon
            FROM loc_civico
            LEFT JOIN loc_indirizzo ON loc_indirizzo.id = loc_civico.id_indirizzo
            LEFT JOIN loc_comune ON loc_comune.id = loc_indirizzo.id_comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia;")->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW materialized_view_autocomplete_cap
            AS
            SELECT DISTINCT ON (loc_civico.cap) CONCAT( loc_civico.cap, ', ', loc_comune.comune, ', (', loc_provincia.sigla, ')' ) as address, loc_civico.lat, loc_civico.lon 
            FROM loc_civico
            LEFT JOIN loc_indirizzo ON loc_indirizzo.id = loc_civico.id_indirizzo
            LEFT JOIN loc_comune ON loc_comune.id = loc_indirizzo.id_comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia;")->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW materialized_view_autocomplete_comuni
            AS
            SELECT DISTINCT ON (loc_comune.id) CONCAT( loc_comune.comune, ', (', loc_provincia.sigla, ')' ) as address, loc_civico.lat, loc_civico.lon 
            FROM loc_civico
            LEFT JOIN loc_indirizzo ON loc_indirizzo.id = loc_civico.id_indirizzo
            LEFT JOIN loc_comune ON loc_comune.id = loc_indirizzo.id_comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_addresses")->execute();
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_cap")->execute();
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_comuni")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181224_163312_add_autocomplete_materialized_views cannot be reverted.\n";

        return false;
    }
    */
}
