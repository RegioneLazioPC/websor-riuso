<?php

namespace common\models;

use Yii;
use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzo;
use backend\events\EditedUtlEventoEvent;
use yii\behaviors\TimestampBehavior;
use common\models\ConOperatoreTaskSearch;


class UtlIngaggioRlFeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_ingaggio_rl_feedback';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ],
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['risorsa','volontari']
            ]
        ];
    }

    public static function getStatoFromClientAction() {
        return [
            'confirm'=>1,
            'refuse'=>2
        ];
    }

    public static function getStati() 
    {
        return [
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
            [['num_elenco_territoriale', 'id_ingaggio', 'stato', 'motivazione_rifiuto'], 'integer'],
            [['note','rl_codfiscale'], 'string'],
            [['risorsa','volontari'],'safe'],
            [['created_at', 'updated_at'], 'safe'],
            [['id_ingaggio'], 'exist', 'skipOnError' => true, 'targetClass' => UtlIngaggio::className(), 'targetAttribute' => ['id_ingaggio' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ingaggio' => 'Attivazione',
            'rl_codfiscale' => 'Codice fiscale',
            'risorsa' => 'Risorsa',
            'volontari' => 'Volontari',
            'note' => 'Note',
            'stato' => 'Stato',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'motivazione_rifiuto' => 'Motivazione rifiuto'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngaggio()
    {
        return $this->hasOne(UtlIngaggio::className(), ['id' => 'id_ingaggio']);
    }


}
