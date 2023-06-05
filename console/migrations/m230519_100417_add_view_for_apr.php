<?php

use yii\db\Migration;

/**
 * Class m230519_100417_add_view_for_apr
 */
class m230519_100417_add_view_for_apr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_attivazioni_apr AS 
            select 
                i.id, i.idevento, i.stato, i.note
                from utl_ingaggio i
                left join utl_automezzo a on i.idautomezzo = a.id
                left join utl_automezzo_tipo t on t.id = a.idtipo
                where t.descrizione ilike '%mezzo apr%' and t.id is not null
                ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_attivazioni_apr
                ")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230519_100417_add_view_for_apr cannot be reverted.\n";

        return false;
    }
    */
}
