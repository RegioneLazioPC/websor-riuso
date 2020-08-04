<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "richiesta_mezzo_aereo".
 *
 * @property integer $id
 * @property string $tipo_intervento
 * @property integer $priorita_intervento
 * @property integer $tipo_vegetazione
 * @property integer $idevento
 * @property integer $idingaggio
 * @property integer $idoperatore
 * @property double $area_bruciata
 * @property double $area_rischio
 * @property integer $fronte_fuoco_num
 * @property integer $fronte_fuoco_tot
 * @property string $elettrodotto
 * @property string $oreografia
 * @property string $vento
 * @property string $ostacoli
 * @property string $note
 * @property string $cfs
 * @property string $sigla_radio_dos
 * @property integer $squadre
 * @property integer $operatori
 * @property string $dataora_inizio_missione
 * @property string $dataora_fine_missione
 * @property string $created_at
 * @property string $updated_at
 * @property array $getEnumFields
 */
class RichiestaMezzoAereo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'richiesta_mezzo_aereo';
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
            [['tipo_intervento', 'elettrodotto', 'oreografia', 'vento', 'ostacoli', 'note'], 'string'],
            [['priorita_intervento', 'tipo_vegetazione', 'fronte_fuoco_num', 'fronte_fuoco_tot', 'squadre', 'operatori', 'idevento'], 'integer'],
            [['area_bruciata', 'area_rischio'], 'number'],
            [['dataora_inizio_missione'], 'required'],
            [['created_at', 'updated_at', 'dataora_inizio_missione', 'dataora_fine_missione'], 'safe'],
            [['cfs', 'sigla_radio_dos'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_intervento' => 'Tipo Intervento',
            'priorita_intervento' => 'Priorita Intervento',
            'tipo_vegetazione' => 'Tipo Vegetazione',
            'area_bruciata' => 'Area Bruciata',
            'area_rischio' => 'Area Rischio',
            'fronte_fuoco_num' => 'Fronte Fuoco Num',
            'fronte_fuoco_tot' => 'Fronte Fuoco Tot',
            'elettrodotto' => 'Elettrodotto',
            'oreografia' => 'Oreografia',
            'vento' => 'Vento',
            'ostacoli' => 'Ostacoli',
            'note' => 'Note',
            'cfs' => 'Cfs',
            'sigla_radio_dos' => 'Sigla Radio Dos',
            'squadre' => 'Squadre',
            'operatori' => 'Operatori',
            'dataora_inizio_missione' => 'Data/ora inizio missione',
            'dataora_fine_missione' => 'Data/ora fine missione',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array
     */
    public static function getEnumFields($type)
    {
        switch ($type){
            case "tipo_intervento":
                $arrayFields = [ 1 => 'Soppressione', 2 => 'Contenimento', 3 => 'Bonifica'];
                break;
            case "elettrodotto":
                $arrayFields = [ 0 => 'Non definito', 1 => 'Nessuno', 2 => 'Da disattivare', 3 => 'A distanza di sicurezza'];
                break;
            case "oreografia":
                $arrayFields = [ 0 => 'Non definito', 1 => 'Pianura', 2 => 'Collina', 3 => 'Montagna', 4 => 'Impervia'];
                break;
            case "vento":
                $arrayFields = [ 0 => 'Non definito', 1 => 'Nessuno', 2 => 'Debole', 3 => 'Moderato', 4 => 'Forte'];
                break;
            case "ostacoli":
                $arrayFields = [ 0 => 'Non definito', 1 => 'Nessuno', 2 => 'Infrastrutture', 3 => 'Abitazioni', 4 => 'Fili a sbalzo - Teleferiche'];
                break;
            default:
                $arrayFields = [];
        }

        return $arrayFields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento']);
    }

}
