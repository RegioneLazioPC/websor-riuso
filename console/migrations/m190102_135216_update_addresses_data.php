<?php

use yii\db\Migration;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleImportStrategy;
/**
 * Class m190102_135216_update_addresses_data
 */
class m190102_135216_update_addresses_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {


        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path.'/data/_autocomplete_addresses.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = '_autocomplete_addresses';
        $config = [
            [
                'attribute' => 'id',
                'value' => function($line) {
                    return intval($line[0]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'full_address',
                'value' => function($line) {
                    return $line[1];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'via',
                'value' => function($line) {
                    return $line[2];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'comune',
                'value' => function($line) {
                    return $line[3];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'provincia',
                'value' => function($line) {
                    return $line[4];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'civici',
                'value' => function($line) {
                    return ($line[5] && $line[5] != '') ? $line[5] : null;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'search_field',
                'value' => function($line) {
                    return $line[6];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'type',
                'value' => function($line) {
                    return $line[7];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'lat',
                'value' => function($line) {
                    return ($line[8] && $line[8] != '') ? $line[8] : null;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'lon',
                'value' => function($line) {
                    return ($line[9] && $line[9] != '') ? $line[9] : null;
                },
                'unique' => false,
            ],
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));


        Yii::$app->db->createCommand("UPDATE _autocomplete_addresses SET search_field =
            setweight(to_tsvector(comune), 'A')    ||
            setweight(to_tsvector(full_address), 'B')  ||
            setweight(to_tsvector(provincia), 'C')
            WHERE \"type\" = 3")->execute();
            
        Yii::$app->db->createCommand("UPDATE _autocomplete_addresses SET search_field =
            setweight(to_tsvector(comune), 'A')    ||
            setweight(to_tsvector(full_address), 'B')  ||
            setweight(to_tsvector(provincia), 'C')
            WHERE \"type\" = 2")->execute();
            
        Yii::$app->db->createCommand("UPDATE _autocomplete_addresses SET search_field =
            setweight(to_tsvector(comune), 'A')    ||
            setweight(to_tsvector(via), 'B')  ||
            setweight(to_tsvector(provincia), 'C')
            WHERE \"type\" = 1")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190102_135216_update_addresses_data cannot be reverted.\n";

        return false;
    }
    */
}
