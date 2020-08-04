<?php

use yii\db\Migration;

/**
 * Class m181227_142742_alter_views_for_autocomplete
 */
class m181227_142742_alter_views_for_autocomplete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_addresses")->execute();
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_cap")->execute();
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_comuni")->execute();

        $this->createTable('loc_full_addresses', [
            'id' => $this->primaryKey(),
            'address' => $this->text(),
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5),
            'comune' => $this->string(),
            'cap' => $this->string(),
            'civico' => $this->string()
        ]);

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW materialized_view_autocomplete_addresses
            AS
            SELECT CONCAT( loc_indirizzo.name, ', ', loc_civico.civico, ', ', loc_civico.cap, ', ', loc_comune.comune, ', (', loc_provincia.sigla, ')' ) as address, loc_civico.lat, loc_civico.lon,
            loc_civico.civico,
            loc_civico.cap,
            loc_comune.comune
            FROM loc_civico
            LEFT JOIN loc_indirizzo ON loc_indirizzo.id = loc_civico.id_indirizzo
            LEFT JOIN loc_comune ON loc_comune.id = loc_indirizzo.id_comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia;")->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW materialized_view_autocomplete_cap
            AS
            SELECT DISTINCT ON (loc_civico.cap) CONCAT( loc_civico.cap, ', ', loc_comune.comune, ', (', loc_provincia.sigla, ')' ) as address, loc_civico.lat, loc_civico.lon,
            '' as civico,
            loc_civico.cap,
            loc_comune.comune
            FROM loc_civico
            LEFT JOIN loc_indirizzo ON loc_indirizzo.id = loc_civico.id_indirizzo
            LEFT JOIN loc_comune ON loc_comune.id = loc_indirizzo.id_comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia;")->execute();

        Yii::$app->db->createCommand("CREATE MATERIALIZED VIEW materialized_view_autocomplete_comuni
            AS
            SELECT DISTINCT ON (loc_comune.id) CONCAT( loc_comune.comune, ', (', loc_provincia.sigla, ')' ) as address, loc_civico.lat, loc_civico.lon,
            '' as civico,
            '' as cap,
            loc_comune.comune
            FROM loc_civico
            LEFT JOIN loc_indirizzo ON loc_indirizzo.id = loc_civico.id_indirizzo
            LEFT JOIN loc_comune ON loc_comune.id = loc_indirizzo.id_comune
            LEFT JOIN loc_provincia ON loc_provincia.id = loc_comune.id_provincia;")->execute();

        Yii::$app->db->createCommand("ALTER TABLE loc_full_addresses ADD \"full_address\" tsvector;")->execute();

        Yii::$app->db->createCommand("CREATE INDEX idx_address_search ON loc_full_addresses USING gin(full_address);")->execute();


        Yii::$app->db->createCommand("INSERT INTO loc_full_addresses(address, lat, lon, comune, cap, civico) SELECT address,lat,lon, comune, cap, civico FROM materialized_view_autocomplete_cap;")->execute();

        Yii::$app->db->createCommand("INSERT INTO loc_full_addresses(address, lat, lon, comune, cap, civico) SELECT address,lat,lon, comune, cap, civico FROM materialized_view_autocomplete_comuni;")->execute();

        Yii::$app->db->createCommand("INSERT INTO loc_full_addresses(address, lat, lon, comune, cap, civico) SELECT address,lat,lon, comune, cap, civico FROM materialized_view_autocomplete_addresses;")->execute();


        Yii::$app->db->createCommand("UPDATE loc_full_addresses SET full_address = to_tsvector(address) WHERE full_address IS NULL;")->execute();


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP INDEX idx_address_search")->execute();
        $this->dropTable('loc_full_addresses');

        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_addresses")->execute();
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_cap")->execute();
        Yii::$app->db->createCommand("DROP MATERIALIZED VIEW IF EXISTS materialized_view_autocomplete_comuni")->execute();

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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181227_142742_alter_views_for_autocomplete cannot be reverted.\n";

        return false;
    }
    */
}
