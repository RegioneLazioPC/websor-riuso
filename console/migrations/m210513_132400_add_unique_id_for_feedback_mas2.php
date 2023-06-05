<?php

use yii\db\Migration;
use yii\db\Schema;


/**
 * Class m210513_132400_add_unique_id_for_feedback_mas2
 */
class m210513_132400_add_unique_id_for_feedback_mas2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->createTable('mas_v2_feedback', [
            'id' => $this->primaryKey(),
            'uid'=>$this->string()->unique(),
            'channel' => $this->string(),
            'id_invio'=>$this->integer(),
            'recapito' => $this->string(),
            'status' => $this->integer(),
            'status_string' => $this->string(),
            'sent_date' => $this->dateTime(),
            'received_date' => $this->dateTime(),
            'refused_date' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-mas_v2_f_i',
            'mas_v2_feedback',
            'id_invio',
            'mas_invio', 
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-mas_v2_f_i',
            'mas_v2_feedback'
        );
        $this->dropTable('mas_v2_feedback');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210513_132400_add_unique_id_for_feedback_mas2 cannot be reverted.\n";

        return false;
    }
    */
}
