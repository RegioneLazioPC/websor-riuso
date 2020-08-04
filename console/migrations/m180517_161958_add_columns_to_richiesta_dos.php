<?php

use yii\db\Migration;

/**
 * Class m180517_161958_add_columns_to_richiesta_dos
 */
class m180517_161958_add_columns_to_richiesta_dos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('richiesta_dos', 'engaged', $this->boolean()->defaultValue(false));
        $this->addColumn('richiesta_dos', 'codicedos', $this->string(100));
        $this->addColumn('richiesta_dos', 'motivo_rifiuto', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'richiesta_dos', 'engaged' );
        $this->dropColumn( 'richiesta_dos', 'codicedos' );
        $this->dropColumn( 'richiesta_dos', 'motivo_rifiuto' );
    }

}
