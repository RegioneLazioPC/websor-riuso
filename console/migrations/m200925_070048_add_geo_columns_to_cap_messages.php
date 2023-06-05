<?php

use yii\db\Migration;

/**
 * Class m200925_070048_add_geo_columns_to_cap_messages
 */
class m200925_070048_add_geo_columns_to_cap_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        

        $this->addColumn('cap_messages', 'poly_geom', 'GEOGRAPHY(GEOMETRY, 4326)');
        $this->addColumn('cap_messages', 'lat', $this->double());
        $this->addColumn('cap_messages', 'lon', $this->double());
        $this->addColumn('cap_messages', 'center_geom', 'GEOMETRY(Point,4326)');
        
        $this->dropColumn('cap_messages', 'ref_identifier');
        $this->dropColumn('cap_messages', 'incident');

        // per info multipli
        $this->addColumn('cap_messages', 'info_n', $this->integer());

        Yii::$app->db->createCommand("CREATE UNIQUE INDEX idx_unique_cap_message_resource
            ON cap_messages(url, info_n);")->execute();

        /**
         * queste due relazioni per i vvff sono sempre 1-1, ma nella realtà possono essere 1-n
         */
        $this->createTable('con_cap_message_incident', [
            'id' => $this->primaryKey(),
            'id_cap_message' => $this->integer(),
            'incident' => $this->string(),
        ]);

        $this->addForeignKey(
            'fk-cap_incident_i',
            'con_cap_message_incident',
            'id_cap_message',
            'cap_messages', 
            'id',
            'CASCADE'
        );

        $this->createTable('con_cap_message_reference', [
            'id' => $this->primaryKey(),
            'id_cap_message' => $this->integer(),
            'reference' => $this->string(),
        ]);

        $this->addForeignKey(
            'fk-cap_reference_i',
            'con_cap_message_reference',
            'id_cap_message',
            'cap_messages', 
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropForeignKey(
            'fk-cap_incident_i',
            'con_cap_message_incident'
        );

        $this->dropForeignKey(
            'fk-cap_reference_i',
            'con_cap_message_reference'
        );

        Yii::$app->db->createCommand("DROP INDEX idx_unique_cap_message_resource;")->execute();

        $this->dropTable('con_cap_message_incident');
        $this->dropTable('con_cap_message_reference');

        $this->addColumn('cap_messages', 'incident', $this->string());
        $this->addColumn('cap_messages', 'ref_identifier', $this->string());

        $this->dropColumn('cap_messages', 'poly_geom');
        $this->dropColumn('cap_messages', 'lat');
        $this->dropColumn('cap_messages', 'lon');
        $this->dropColumn('cap_messages', 'center_geom');
        $this->dropColumn('cap_messages', 'info_n');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_070048_add_geo_columns_to_cap_messages cannot be reverted.\n";

        return false;
    }
    */
}
