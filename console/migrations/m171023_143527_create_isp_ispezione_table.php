<?php

use yii\db\Migration;

/**
 * Handles the creation of table `isp_ispezione`.
 */
class m171023_143527_create_isp_ispezione_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('isp_ispezione');

        if ($tableSchema === null) {
            if ($this->db->driverName === 'pgsql') {
                Yii::$app->db->createCommand("CREATE TYPE isp_ispezione_tipo_attivita AS ENUM ('Ordinaria', 'Straordinaria')")
                ->execute();
            }

            $this->createTable('isp_ispezione', [
                'id' => $this->primaryKey(),
                'idoperatore' => $this->integer(11),
                'idtipologia' => $this->integer(11),
                'lat' => $this->decimal(11,5),
                'lon' => $this->decimal(11,5),
                'idcomune' => $this->integer(11),
                'indirizzo' => $this->string(255),
                'tipo_attivita' => "isp_ispezione_tipo_attivita"
            ]);

            // add foreign key for table `user`
            $this->addForeignKey(
                'fk-idoperatore',
                'isp_ispezione',
                'idoperatore',
                'utl_operatore_pc',
                'id'
            );

            // add foreign key for table `user`
            $this->addForeignKey(
                'fk-idtipologia',
                'isp_ispezione',
                'idtipologia',
                'utl_tipologia',
                'id'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        

        $this->dropForeignKey(
            'fk-idoperatore',
            'isp_ispezione'
        );

        $this->dropForeignKey(
            'fk-idtipologia',
            'isp_ispezione'
        );

        $this->dropTable('isp_ispezione');

        Yii::$app->db->createCommand("DROP TYPE isp_ispezione_tipo_attivita")
            ->execute();
    }
}
