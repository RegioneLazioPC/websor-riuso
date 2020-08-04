<?php

namespace common\models\tabelle;

use Yii;
use yii\behaviors\TimestampBehavior;


class TblTipoRisorsaMeta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_tipo_risorsa_meta';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['static_data']
            ]          
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'show_in_column', 'created_at', 'updated_at'], 'integer'],
            [
                [
                    'extra',
                    'key',
                    'ref_id',
                    'label',
                    'id_sync'
                ], 
                'string'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Tipo',
            'show_in_column' => 'Mostra in colonna',
            'extra' => 'Extra',
            'key' => 'Chiave',
            'ref_id' => 'Riferimento ZeroGIS',
            'label' => 'Label',
            'id_sync' => 'Riferimento MGO',
            'created_at' => 'Creazione',
            'updated_at' => 'Ultimo aggiornamento',
        ];
    }

    public static function filterOptionsType() {
        return [
            1 => 'Testo',
            2 => 'Numero',
            3 => 'Opzione',
            4 => 'Opzione',
            5 => 'Si/No',
            6 => 'Data',
            7 => 'Data ora'
        ];
    }

    public static function filterOptionsColumn() {
        return [
            0 => 'No',
            1 => 'Si'
        ];
    }

    public function tipo() {
        switch($this->type) {
            case 1:
                return 'Testo';
            break;
            case 2:
                return 'Numero';
            break;
            case 3:
                return 'Opzione';
            break;
            case 4:
                return 'Opzione';
            break;
            case 5:
                return 'Si/No';
            break;
            case 6:
                return 'Data';
            break;
            case 7:
                return 'Data ora';
            break;
            default: 
                return $this->type;
            break;
        }
    }

    public function inColumn() {
        return $this->show_in_column == 1 ? 'Si' : 'No';
    }

    public function extra() {
        return str_replace(":::", "; ", $this->extra);
    }
}
