<?php

namespace common\models;

use Yii;
use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzo;
use backend\events\EditedUtlEventoEvent;

use common\models\ConOperatoreTaskSearch;
/**
 * This is the model class for table "utl_ingaggio".
 *
 * @property int $id
 * @property int $idevento
 * @property int $idorganizzazione
 * @property int $idsede
 * @property int $idautomezzo
 * @property int $idattrezzatura
 * @property string $note
 * @property int $stato
 * @property string $created_at
 * @property string $updated_at
 * @property string $closed_at
 *
 * @property UtlAttrezzatura $attrezzatura
 * @property UtlAutomezzo $automezzo
 * @property UtlEvento $evento
 * @property VolOrganizzazione $organizzazione
 * @property VolSede $sede
 */
class UtlIngaggio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_ingaggio';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['static_data']
            ]
        ];
    }

    public static function getStati() 
    {
        return [
            0=>'In attesa di conferma',
            1=>'Confermato',
            2=>'Rifiutato',
            3=>'Chiuso'
        ];
    }

    public static function getMotivazioniRifiuto() 
    {
        return [
            1=>'FUORI ORARIO',
            2=>'NON RISPONDE',
            3=>'MEZZO NON DISPONIBILE',
            4=>'SQUADRA NON DISPONIBILE',
            6=>'IMPEGNATA CON ALTRO ENTE',
            5=>'ALTRO'
        ];
    }

    public static function replaceStato($stato)
    {
        switch($stato){
            case 0:
            return 'In attesa di conferma';
            break;
            case 1:
            return 'Confermato';
            break;
            case 2:
            return 'Rifiutato';
            break;
            case 3:
            return 'Chiuso';
            break;
            default:
            return '';
            break;
        }
    }

    public static function replaceMotivazioneRifiuto($motivazione)
    {
        switch($motivazione){
            case 1:
            return 'FUORI ORARIO';
            break;
            case 2:
            return 'NON RISPONDE';
            break;
            case 3:
            return 'MEZZO NON DISPONIBILE';
            break;
            case 4:
            return 'SQUADRA NON DISPONIBILE';
            break;
            case 6:
            return 'IMPEGNATA CON ALTRO ENTE';
            break;
            case 5:
            return 'ALTRO';
            break;
            default:
            return '';
            break;
        }
    }

    public static function returnStatoColor($stato)
    {
        switch($stato){
            case 0:
            return 'yellow';
            break;
            case 1:
            return 'green';
            break;
            case 2:
            return 'red';
            break;
            case 3:
            return 'grey';
            break;
        }
    }

    public function getStato()
    {
        return UtlIngaggio::replaceStato($this->stato);
    }

    public function getMotivazioneRifiuto()
    {
        return UtlIngaggio::replaceMotivazioneRifiuto($this->motivazione_rifiuto);
    }

    public function getStatoColor()
    {
        return UtlIngaggio::returnStatoColor($this->stato);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevento', 'idorganizzazione', 'idsede', 'idautomezzo', 'idattrezzatura', 'stato'], 'default', 'value' => null],
            [['idevento', 'idorganizzazione', 'idsede', 'idautomezzo', 'idattrezzatura', 'stato', 'motivazione_rifiuto'], 'integer'],
            [['note', 'motivazione_rifiuto_note'], 'string'],
            [['created_at', 'updated_at', 'closed_at'], 'safe'],
            [['idattrezzatura'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAttrezzatura::className(), 'targetAttribute' => ['idattrezzatura' => 'id']],
            [['idautomezzo'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAutomezzo::className(), 'targetAttribute' => ['idautomezzo' => 'id']],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
            [['idorganizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['idorganizzazione' => 'id']],
            [['idsede'], 'exist', 'skipOnError' => true, 'targetClass' => VolSede::className(), 'targetAttribute' => ['idsede' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idevento' => 'Idevento',
            'idorganizzazione' => 'Idorganizzazione',
            'idsede' => 'Idsede',
            'idautomezzo' => 'Idautomezzo',
            'idattrezzatura' => 'Idattrezzatura',
            'note' => 'Note',
            'stato' => 'Stato',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'closed_at' => 'Closed At',
            'motivazione_rifiuto' => 'Motivazione rifiuto',
            'motivazione_rifiuto_note' => 'Note motivazione rifiuto'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttrezzatura()
    {
        return $this->hasOne(UtlAttrezzatura::className(), ['id' => 'idattrezzatura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutomezzo()
    {
        return $this->hasOne(UtlAutomezzo::className(), ['id' => 'idautomezzo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'idorganizzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSede()
    {
        return $this->hasOne(VolSede::className(), ['id' => 'idsede']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolontari()
    {
        return $this->hasMany(VolVolontario::className(), ['id' => 'id_volontario'])
        ->viaTable('con_volontario_ingaggio', ['id_ingaggio'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function beforeDelete( )
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if($this->idautomezzo) : 
            UtlAutomezzo::updateAll(['engaged'=>false], 'id = '.intval($this->idautomezzo)); 
        endif;
                
        if($this->idattrezzatura) : 
            UtlAttrezzatura::updateAll(['engaged'=>false], 'id = '.intval($this->idattrezzatura));
        endif;

        return true;
                    
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function beforeSave($insert)
    {
        if ($insert) :
            $this->stato = 0;
            $this->created_at = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');
            $this->updated_at = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');
            if($this->idautomezzo) : UtlAutomezzo::updateAll(['engaged'=>true], 'id = '.intval($this->idautomezzo)); endif;
            if($this->idattrezzatura) : UtlAttrezzatura::updateAll(['engaged'=>true], 'id = '.intval($this->idattrezzatura)); endif;
        else :
            $this->updated_at = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');
        endif;

        if($this->stato == 3 && !$this->closed_at) :
            $this->closed_at = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');
        endif;

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function afterSave($insert, $changedAttributes) 
    {
       
        parent::afterSave($insert, $changedAttributes);

        
        if($insert) {
            $json = [];
            $json['organizzazione'] = [
                'id' => @$this->organizzazione->id,
                'denominazione' => @$this->organizzazione->denominazione,
                'num_elenco_territoriale' => @$this->organizzazione->ref_id,
                'num_comunale' => (!empty($this->organizzazione->num_comunale)) ? @$this->organizzazione->num_comunale : ''
            ];
            $json['sede'] = [
                'id' => @$this->sede->id,
                'via' => @$this->sede->indirizzo,
                'comune' => @$this->sede->locComune->comune,
                'cap' => @$this->sede->cap,
                'nome' => @$this->sede->name
            ];
            $json['automezzo'] = [];
            $json['attrezzatura'] = [];
            if($this->idautomezzo) {
                $json['automezzo'] = [
                    'tipo' => @$this->automezzo->tipo->descrizione,
                    'id' => @$this->idautomezzo,
                    'targa' => @$this->automezzo->targa,
                    'tempo_attivazione' => @$this->automezzo->tempo_attivazione,
                    'allestimento' => @$this->automezzo->allestimento,
                    'data_immatricolazione' => @$this->automezzo->data_immatricolazione,
                    'classe' => @$this->automezzo->classe,
                    'sottoclasse' => @$this->automezzo->sottoclasse,
                    'modello' => @$this->automezzo->modello,
                ];
            }

            if($this->idattrezzatura) {
                $json['attrezzatura'] = [
                    'tipo' => @$this->attrezzatura->tipo->descrizione,
                    'id' => @$this->idattrezzatura,
                    'tempo_attivazione' => @$this->attrezzatura->tempo_attivazione,
                    'allestimento' => @$this->attrezzatura->allestimento,
                    'classe' => @$this->attrezzatura->classe,
                    'sottoclasse' => @$this->attrezzatura->sottoclasse,
                    'modello' => @$this->attrezzatura->modello,
                    'unita' => @$this->attrezzatura->unita,
                ];
            }

            $conn = \Yii::$app->db;
            $conn->createCommand()->update('utl_ingaggio', [
                'static_data' => $json
            ], [ 'id' => $this->id ])->execute();
            
        }
        
        
        if (isset($changedAttributes['stato'])) :
            
            switch($this->stato):
                case 1:
                if($this->idautomezzo) : UtlAutomezzo::updateAll(['engaged'=>true], 'id = '.intval($this->idautomezzo)); endif;
                if($this->idattrezzatura) : UtlAttrezzatura::updateAll(['engaged'=>true], 'id = '.intval($this->idattrezzatura)); endif;
                    $task_name = 'Conferma attivazione - '.Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s').'<br />';
                break;
                case 2: case 3:
                if($this->idautomezzo) : UtlAutomezzo::updateAll(['engaged'=>false], 'id = '.intval($this->idautomezzo)); endif;
                if($this->idattrezzatura) : UtlAttrezzatura::updateAll(['engaged'=>false], 'id = '.intval($this->idattrezzatura)); endif;
                    $task_name = ($this->stato == 2) ? 'Rifiuto attivazione' : 'Chiusura attivazione - '.Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s').'<br />';

                    if($this->stato == 2) :
                        $task_name .= $this->getMotivazioneRifiuto().'<br />';
                    endif;
                break;
                default:
                    $task_name = 'Modifica attivazione';
                break;
            endswitch;


            if($this->idautomezzo) :
                $automezzo = UtlAutomezzo::find()->where(['id'=>$this->idautomezzo])->one();
                if($automezzo && !empty($automezzo->targa)) $task_name .= " - " . $automezzo->targa . " -";
            endif;
            $diarioEvento = new ConOperatoreTask();
            $diarioEvento->idfunzione_supporto = 5; //DATI CABLATI NEL DB
            $diarioEvento->idtask = 4; //DATI CABLATI NEL DB
            $diarioEvento->idevento = $this->idevento;
            $diarioEvento->note = $task_name . ' ' . $this->organizzazione->ref_id . ' - ' . $this->organizzazione->denominazione;
            $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;
            $diarioEvento->save();

        endif;

        EditedUtlEventoEvent::handleEdited($this->idevento);
    }


}
