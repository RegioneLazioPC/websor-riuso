<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use backend\events\EditedUtlEventoEvent;

use common\models\UtlTipologia;

/**
 * This is the model class for table "richiesta_elicottero".
 *
 * @property int $id
 * @property int $idevento
 * @property int $idingaggio
 * @property int $idoperatore
 * @property string $tipo_intervento
 * @property int $priorita_intervento
 * @property int $tipo_vegetazione
 * @property double $area_bruciata
 * @property double $area_rischio
 * @property int $fronte_fuoco_num
 * @property int $fronte_fuoco_tot
 * @property string $elettrodotto
 * @property string $oreografia
 * @property string $vento
 * @property string $ostacoli
 * @property string $note
 * @property string $cfs
 * @property string $sigla_radio_dos
 * @property bool $squadre
 * @property int $operatori
 * @property bool $engaged
 * @property string $motivo_rifiuto
 * @property string $codice_elicottero
 * @property string $created_at
 * @property string $updated_at
 */
class RichiestaElicottero extends \yii\db\ActiveRecord
{
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_PARTIAL_UPDATE = 'partial_update';
    const SCENARIO_SEND_COAU = 'send_coau';

    public $date;
    public $hour;
    public $minutes;
    public $date_arrivo_stimato;
    public $hour_arrivo_stimato;
    public $minutes_arrivo_stimato;
    public $date_atterraggio;
    public $hour_atterraggio;
    public $minutes_atterraggio;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'richiesta_elicottero';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            // Other behaviors
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idevento', 'idingaggio', 'idoperatore', 'priorita_intervento', 'tipo_vegetazione', 'fronte_fuoco_num', 'fronte_fuoco_tot', 'operatori', 'id_anagrafica_funzionario'], 'default', 'value' => null],
            [['edited'], 'default', 'value' => 0],
            [['idevento', 'idingaggio', 'idoperatore', 'priorita_intervento', 'tipo_vegetazione', 'fronte_fuoco_num', 'fronte_fuoco_tot', 'operatori', 'edited', 'id_anagrafica_funzionario'], 'integer'],
            [['tipo_intervento', 'elettrodotto', 'oreografia', 'vento', 'ostacoli', 'note'], 'string'],
            [['area_bruciata', 'area_rischio'], 'number'],
            [['squadre'], 'boolean'],
            [[
                'deviato',
                'dos',
                'squadre_volontariato',
                'squadre_vvf',
                'id_tipo_evento'
            ], 'integer'],
            //[['created_at'], 'required'],
            [['created_at', 'updated_at', 'codice_elicottero', 'motivo_rifiuto', 'dataora_decollo'], 'safe'],
            [['dataora_arrivo_stimato', 'dataora_atterraggio'], 'date', 'format'=>'php:Y-m-d H:i'],
            ['engaged', 'boolean'],
            [['cfs', 'sigla_radio_dos'], 'string', 'max' => 255],
            [['id_elicottero', 'id_comune'], 'integer'],
            [['localita', 'missione'], 'string'],
            [['hour'], 'number', 'min'=>0, 'max'=>24],
            [['minutes'], 'number', 'min'=>0, 'max'=>59],
            [['n_lanci'], 'integer'],

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
            'idingaggio' => 'Idingaggio',
            'idoperatore' => 'Idoperatore',
            'dataora_arrivo_stimato' => 'Arrivo stimato',
            'dataora_atterraggio' => 'Atterraggio',
            'deleted' => 'Annullata',
            'n_lanci' => 'Numero lanci',
            'tipo_intervento' => 'Tipo Intervento',
            'priorita_intervento' => 'Priorita Intervento',
            'tipo_vegetazione' => 'Tipo Vegetazione',
            'area_bruciata' => 'Area Bruciata',
            'area_rischio' => 'Area Rischio',
            'fronte_fuoco_num' => 'Fronte Fuoco Num',
            'fronte_fuoco_tot' => 'Fronte Fuoco Tot',
            'elettrodotto' => 'Elettrodotto',
            'oreografia' => 'Orografia',
            'vento' => 'Vento',
            'ostacoli' => 'Ostacoli',
            'note' => 'Motivo del rifiuto',
            'cfs' => 'Cfs',
            'sigla_radio_dos' => 'Sigla Radio Dos',
            'squadre' => 'Squadre',
            'operatori' => 'Operatori',
            'codice_elicottero' => 'Codice Elicottero',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'motivo_rifiuto' => 'Note',
            'date'=>'Data',
            'hour'=>'ore',
            'minutes'=>'min.'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = [
            'idevento',
            'idingaggio',
            'idoperatore',
            //'priorita_intervento',
            //'tipo_vegetazione',
            'fronte_fuoco_num',
            'fronte_fuoco_tot',
            'operatori',
            'edited',
            'tipo_intervento',
            'elettrodotto',
            'oreografia',
            'vento',
            'ostacoli',
            'note',
            'area_bruciata',
            'area_rischio',
            'squadre',
            'codice_elicottero',
            'motivo_rifiuto',
            'engaged',
            'cfs',
            'sigla_radio_dos',
            'id_elicottero',
            'id_comune',
            'localita',
            'missione',
            'dataora_decollo',
            'hour',
            'minutes',
            'dataora_arrivo_stimato',
            'dataora_atterraggio',
            'n_lanci',
        ];
        $scenarios[self::SCENARIO_PARTIAL_UPDATE] = [
            'idevento',
            'idingaggio',
            'idoperatore',
            //'priorita_intervento',
            //'tipo_vegetazione',
            'fronte_fuoco_num',
            'fronte_fuoco_tot',
            'operatori',
            'tipo_intervento',
            'elettrodotto',
            'oreografia',
            'vento',
            'ostacoli',
            'note',
            'area_bruciata',
            'area_rischio',
            'squadre',
            'codice_elicottero',
            'motivo_rifiuto',
            'cfs',
            'sigla_radio_dos',
            'id_elicottero',
            'id_comune',
            'localita',
            'missione',
            'dataora_decollo',
            'hour',
            'minutes',
            'dataora_arrivo_stimato',
            'dataora_atterraggio',
            'n_lanci',
        ]; 
        $scenarios[self::SCENARIO_SEND_COAU] = [
            'id_anagrafica_funzionario',
            'deviato',
            'dos',
            'squadre_volontariato',
            'squadre_vvf',
            'id_tipo_evento'
        ];
        return $scenarios;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatore()
    {
        return $this->hasOne(UtlOperatorePc::className(), ['id' => 'idoperatore']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElicottero()
    {
        return $this->hasOne(UtlAutomezzo::className(), ['id' => 'id_elicottero']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento']);
    }

    /**
     * @return [type] [description]
     */
    public function getTipoEvento() 
    {
        return $this->hasOne(UtlTipologia::className(), ['id' => 'id_tipo_evento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'id_comune']);
    }

    public function getFunzionario() {
        return $this->hasOne(UtlAnagrafica::className(), ['id'=>'id_anagrafica_funzionario']);
    }

    public function afterSave($insert, $changedAttributes) 
    {
        parent::afterSave($insert, $changedAttributes);
        
        EditedUtlEventoEvent::handleEdited($this->idevento);
    }
}
