<?php

use yii\db\Migration;

use common\models\EvtGestoreEvento;
/**
 * Handles the creation of table `evt_gestore_evento`.
 */
class m190502_173043_create_evt_gestore_evento_table extends Migration
{
    public $gestori = ['COMUNE', 'VV.FF'];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('evt_gestore_evento', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string()
        ]);

        $this->addColumn('utl_evento', 'id_gestore_evento', $this->integer());

        $this->addForeignKey(
            'fk-evt_evento_gestore',
            'utl_evento',
            'id_gestore_evento',
            'evt_gestore_evento',
            'id',
            'SET NULL'
        );

        foreach($this->gestori as $gestore){
            $gest = EvtGestoreEvento::find()->where(['descrizione'=>$gestore])->one();
            if(!$gest) $gest = new EvtGestoreEvento;

            $gest->descrizione = $gestore;
            $gest->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-evt_evento_gestore',
            'utl_evento'
        );

        $this->dropColumn('utl_evento', 'id_gestore_evento');

        $this->dropTable('evt_gestore_evento');
    }
}
