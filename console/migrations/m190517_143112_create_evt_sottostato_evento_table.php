<?php

use yii\db\Migration;

use common\models\EvtSottostatoEvento;
use common\models\UtlTipologia;
/**
 * Handles the creation of table `evt_sottostato_evento`.
 */
class m190517_143112_create_evt_sottostato_evento_table extends Migration
{

    private $sottostati_incendio = [
        'Verifica','Spegnimento','Bonifica','Controllo'
    ];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('evt_sottostato_evento', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string()
        ]);

        $this->createTable('con_evt_sottostato_evento_utl_evento', [
            'id' => $this->primaryKey(),
            'id_tipo_evento' => $this->integer(),
            'id_sottostato_evento' => $this->integer()
        ]);

        $this->addColumn('utl_evento','id_sottostato_evento', $this->integer());

        $this->addForeignKey(
            'fk-con_evt_sottostato_evento_utl_evento_evento',
            'con_evt_sottostato_evento_utl_evento',
            'id_tipo_evento',
            'utl_tipologia', // ma perchè utl_tipologia???
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-con_evt_sottostato_evento_utl_evento_sottostato',
            'con_evt_sottostato_evento_utl_evento',
            'id_sottostato_evento',
            'evt_sottostato_evento', // ma perchè utl_tipologia???
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-utl_evento_sottostato',
            'utl_evento',
            'id_sottostato_evento',
            'evt_sottostato_evento', 
            'id',
            'SET NULL'
        );


        /**
         * Inseriamo associazione incendio sottostati
         */
        $incendio = UtlTipologia::find()->where(['tipologia'=>'Incendio'])->orderBy(['id'=>SORT_ASC])->one();
        if($incendio) {
            foreach ($this->sottostati_incendio as $sottostato) {
                $s = EvtSottostatoEvento::find()->where(['descrizione'=>$sottostato])->one();
                if(!$s) $s = new EvtSottostatoEvento;
                $s->descrizione = $sottostato;
                $s->save();

                $s->link('tipoEvento', $incendio);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-con_evt_sottostato_evento_utl_evento_evento',
            'con_evt_sottostato_evento_utl_evento'
        );

        $this->dropForeignKey(
            'fk-con_evt_sottostato_evento_utl_evento_sottostato',
            'con_evt_sottostato_evento_utl_evento'
        );

        $this->dropForeignKey(
            'fk-utl_evento_sottostato',
            'utl_evento'
        );

        $this->dropColumn('utl_evento','id_sottostato_evento');
        $this->dropTable('con_evt_sottostato_evento_utl_evento');
        $this->dropTable('evt_sottostato_evento');
    }
}
