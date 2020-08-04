<?php

use yii\db\Migration;

use common\models\RichiestaElicottero;
/**
 * Class m190521_165738_add_missione_to_old_richieste_elicottero
 */
class m190521_165738_add_missione_to_old_richieste_elicottero extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $richieste = RichiestaElicottero::find()->all();
        foreach ($richieste as $richiesta) {
            $richiesta->missione = strtoupper($richiesta->tipo_intervento);
            $richiesta->save();
        }
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
        echo "m190521_165738_add_missione_to_old_richieste_elicottero cannot be reverted.\n";

        return false;
    }
    */
}
