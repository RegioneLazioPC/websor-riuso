<?php

use yii\db\Migration;

/**
 * Handles adding sottotipologia_evento to table `utl_evento`.
 */
class m180410_130923_add_sottotipologia_evento_column_to_utl_evento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('utl_evento', 'sottotipologia_evento', $this->integer()->after('tipologia_evento'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('utl_evento', 'sottotipologia_evento');
    }
}
