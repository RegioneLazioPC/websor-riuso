<?php

use yii\db\Migration;

/**
 * Class m180418_163807_add_colonne_attrezzature_automezzi
 */
class m180418_163807_add_colonne_attrezzature_automezzi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_automezzo', 'tempo_attivazione', $this->string(255));
        $this->addColumn('utl_attrezzatura', 'tempo_attivazione', $this->string(255));
        $this->addColumn('utl_automezzo', 'allestimento', $this->string(255));
        $this->addColumn('utl_attrezzatura', 'allestimento', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_automezzo', 'tempo_attivazione');
        $this->dropColumn('utl_attrezzatura', 'tempo_attivazione');
        $this->dropColumn('utl_automezzo', 'allestimento');
        $this->dropColumn('utl_attrezzatura', 'allestimento');
    }

}
