<?php

use yii\db\Migration;

/**
 * Class m220912_125250_create_view_view_mezzi
 */
class m220912_125250_create_view_view_mezzi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_targhe_mezzi AS
            with trg as (select \"key\" from tbl_tipo_risorsa_meta ttrm where ttrm.\"label\" ilike 'targa')
            select distinct jkeys.\"value\"::text as targa, ua.id, ua.idorganizzazione
                from utl_automezzo ua, jsonb_each(ua.meta) jkeys 
                where 
                jsonb_typeof(ua.meta) = 'object' and jkeys.\"key\"::text = (select trg.\"key\" from trg limit 1)
            and jkeys.\"value\"::text <> '' and jkeys.\"value\" is not null;")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_targhe_mezzi")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220912_125250_create_view_view_mezzi cannot be reverted.\n";

        return false;
    }
    */
}
