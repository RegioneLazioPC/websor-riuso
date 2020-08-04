<?php

use yii\db\Migration;

/**
 * Handles the creation of table `richiesta_elicottero`.
 */
class m180517_130743_create_richiesta_elicottero_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'pgsql') {


            Yii::$app->db->createCommand("CREATE TYPE richiesta_elicottero_tipo_intervento AS ENUM ('Soppressione','Rico-Armata','Ricognizione')")
                ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_elicottero_elettrodotto AS ENUM ('Non definito','Nessuno','Da disattivare','A distanza di sicurezza')")
                ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_elicottero_oreografia AS ENUM ('Non definito','Pianura','Collina','Montagna','Impervia')")
                ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_elicottero_vento AS ENUM ('Non definito','Nessuno','Debole','Moderato','Forte')")
                ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_elicottero_ostacoli AS ENUM ('Non definito','Nessuno','Infrastrutture','Abitazioni','Fili a sbalzo - Teleferiche')")
                ->execute();


        }

        $this->createTable('richiesta_elicottero', [
            'id' => $this->primaryKey(),
            'idevento' => $this->integer(11),
            'idingaggio' => $this->integer(11),
            'idoperatore' => $this->integer(11),
            'tipo_intervento' => "richiesta_elicottero_tipo_intervento",
            'priorita_intervento' => $this->integer()->notNull()->defaultValue(1),
            'tipo_vegetazione' => $this->integer()->notNull()->defaultValue(1),
            'area_bruciata' => $this->float(),
            'area_rischio' => $this->float(),
            'fronte_fuoco_num' => $this->integer(),
            'fronte_fuoco_tot' => $this->integer(),
            'elettrodotto' => "richiesta_elicottero_elettrodotto",
            'oreografia' => "richiesta_elicottero_oreografia",
            'vento' => "richiesta_elicottero_vento",
            'ostacoli' => "richiesta_elicottero_ostacoli",
            'note' =>$this->text(),
            'cfs' =>$this->string(),
            'sigla_radio_dos' =>$this->string(),
            'squadre' =>$this->boolean(),
            'operatori' =>$this->integer(11),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {

        $this->dropTable('richiesta_elicottero');

        Yii::$app->db->createCommand("DROP TYPE richiesta_elicottero_tipo_intervento")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_elicottero_elettrodotto")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_elicottero_oreografia")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_elicottero_vento")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_elicottero_ostacoli")
            ->execute();


    }
}
