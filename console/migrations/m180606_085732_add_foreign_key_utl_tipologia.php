<?php

use yii\db\Migration;

/**
 * Class m180606_085732_add_foreign_key_utl_tipologia
 */
class m180606_085732_add_foreign_key_utl_tipologia extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk_tipologia_evento',
            'utl_evento'
        );

        $this->dropForeignKey(
            'fk_id_tipologia',
            'utl_segnalazione'
        );

        $this->addForeignKey(
            'fk_tipologia_evento',
            'utl_evento',
            'tipologia_evento',
            'utl_tipologia',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_sottotipologia_evento',
            'utl_evento',
            'sottotipologia_evento',
            'utl_tipologia',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_id_tipologia',
            'utl_segnalazione',
            'tipologia_evento',
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
            'fk_tipologia_evento',
            'utl_evento'
        );

        $this->dropForeignKey(
            'fk_sottotipologia_evento',
            'utl_evento'
        );

        $this->dropForeignKey(
            'fk_id_tipologia',
            'utl_segnalazione'
        );

        $this->addForeignKey(
            'fk_tipologia_evento',
            'utl_evento',
            'tipologia_evento',
            'utl_tipologia',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_id_tipologia',
            'utl_segnalazione',
            'tipologia_evento',
            'utl_tipologia',
            'id',
            'NO ACTION'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180606_085732_add_foreign_key_utl_tipologia cannot be reverted.\n";

        return false;
    }
    */
}
