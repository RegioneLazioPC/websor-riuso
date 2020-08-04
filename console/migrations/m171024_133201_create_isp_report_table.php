<?php

use yii\db\Migration;

/**
 * Handles the creation of table `isp_report`.
 */
class m171024_133201_create_isp_report_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('isp_report');

        if ($tableSchema === null) {
            $this->createTable('isp_report', [
                'id' => $this->primaryKey(),
                'idispezione' => $this->integer(11),
                'idtipo_fenomeno' => $this->integer(11),
                'idelemento_esposto' => $this->integer(11),
                'iddescrizione' => $this->integer(11),
                'idconclusioni' => $this->integer(11),
                'volume' => $this->decimal(11,5),
                'lunghezza' => $this->decimal(11,5),
                'larghezza' => $this->decimal(11,5),
                'n_abitanti' => $this->integer(11),
                'descrizione' => $this->text(),
                'intervento_brevetermine' => $this->text(),
                'intervento_lungotermine' => $this->text(),
                'richiami_amministrazione' => $this->text()
            ]);

            // add foreign key for table `user`
            $this->addForeignKey(
                'fk-idispezione',
                'isp_report',
                'idispezione',
                'isp_ispezione',
                'id'
            );

            // add foreign key for table `user`
            $this->addForeignKey(
                'fk-idtipo-fenomeno',
                'isp_report',
                'idtipo_fenomeno',
                'isp_tipo_fenomeno',
                'id'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-idispezione',
            'isp_report'
        );

        $this->dropForeignKey(
            'fk-idtipo-fenomeno',
            'isp_report'
        );

        $this->dropTable('isp_report');
    }
}
