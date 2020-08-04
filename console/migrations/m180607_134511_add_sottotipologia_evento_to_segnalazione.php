<?php

use yii\db\Migration;

/**
 * Class m180607_134511_add_sottotipologia_evento_to_segnalazione
 */
class m180607_134511_add_sottotipologia_evento_to_segnalazione extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_segnalazione', 'sottotipologia_evento', $this->integer());
        $this->addForeignKey(
            'fk_segnalazione_sottotipologia',
            'utl_segnalazione',
            'sottotipologia_evento',
            'utl_tipologia',
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
            'fk_segnalazione_sottotipologia',
            'utl_segnalazione'
        );
        $this->dropColumn('utl_segnalazione', 'sottotipologia_evento');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_134511_add_sottotipologia_evento_to_segnalazione cannot be reverted.\n";

        return false;
    }
    */
}
