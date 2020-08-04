<?php

use yii\db\Migration;

/**
 * Class m190102_113338_autocomplete_addresses_table
 */
class m190102_113338_autocomplete_addresses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('_autocomplete_addresses', [
            'id' => $this->primaryKey(),
            'full_address' => $this->text(),
            'via' => $this->string(),
            'comune' => $this->string(),
            'provincia' => $this->string(),
            'civici' => 'json',
            'search_field' => 'tsvector',
            'type' => $this->integer(1),
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5),
        ]);

        Yii::$app->db->createCommand("CREATE INDEX search_address_idx ON _autocomplete_addresses USING GIN (search_field);")->execute();


        /*$path = Yii::getAlias("@app");
        Yii::$app->db->createCommand("copy _autocomplete_addresses FROM '".$path."/data/_autocomplete_addresses.csv' DELIMITER ',' CSV;")->execute();*/
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP INDEX search_address_idx ON _autocomplete_addresses;")->execute();
        $this->dropTable('_autocomplete_addresses');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190102_113338_autocomplete_addresses_table cannot be reverted.\n";

        return false;
    }
    */
}
