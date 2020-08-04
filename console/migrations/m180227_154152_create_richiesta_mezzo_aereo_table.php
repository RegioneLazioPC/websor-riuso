<?php

use yii\db\Migration;

/**
 * Handles the creation of table `richiesta_mezzo_aereo`.
 */
class m180227_154152_create_richiesta_mezzo_aereo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'pgsql') {
            
            
            Yii::$app->db->createCommand("CREATE TYPE richiesta_mezzo_aereo_tipo_intervento AS ENUM ('Soppressione','Rico-Armata','Ricognizione')")
            ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_mezzo_aereo_elettrodotto AS ENUM ('Non definito','Nessuno','Da disattivare','A distanza di sicurezza')")
            ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_mezzo_aereo_oreografia AS ENUM ('Non definito','Pianura','Collina','Montagna','Impervia')")
            ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_mezzo_aereo_vento AS ENUM ('Non definito','Nessuno','Debole','Moderato','Forte')")
            ->execute();

            Yii::$app->db->createCommand("CREATE TYPE richiesta_mezzo_aereo_ostacoli AS ENUM ('Non definito','Nessuno','Infrastrutture','Abitazioni','Fili a sbalzo - Teleferiche')")
            ->execute();

            
        }



        $this->createTable('richiesta_mezzo_aereo', [
            'id' => $this->primaryKey(),
            'tipo_intervento' => "richiesta_mezzo_aereo_tipo_intervento",
            'priorita_intervento' => $this->integer()->notNull()->defaultValue(1),
            'tipo_vegetazione' => $this->integer()->notNull()->defaultValue(1),
            'area_bruciata' => $this->float(),
            'area_rischio' => $this->float(),
            'fronte_fuoco_num' => $this->integer(),
            'fronte_fuoco_tot' => $this->integer(),
            'elettrodotto' => "richiesta_mezzo_aereo_elettrodotto",
            'oreografia' => "richiesta_mezzo_aereo_oreografia",
            'vento' => "richiesta_mezzo_aereo_vento",
            'ostacoli' => "richiesta_mezzo_aereo_ostacoli",
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
    public function safeDown()
    {$this->dropTable('richiesta_mezzo_aereo');
        Yii::$app->db->createCommand("DROP TYPE richiesta_mezzo_aereo_tipo_intervento")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_mezzo_aereo_elettrodotto")
        ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_mezzo_aereo_oreografia")
        ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_mezzo_aereo_vento")
        ->execute();

        Yii::$app->db->createCommand("DROP TYPE richiesta_mezzo_aereo_ostacoli")
        ->execute();

        
    }
}
