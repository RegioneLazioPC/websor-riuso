<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzo;
use common\models\LocComune;
use common\models\LocProvincia;
use common\models\UtlEvento;

use common\models\utility\UtlContatto;
use common\models\organizzazione\ConOrganizzazioneContatto;
use nanson\postgis\behaviors\GeometryBehavior;


class UtlIngaggioSearchForm extends \yii\db\ActiveRecord
{
    public $specializzazioni;
    
    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["ref_id"];
    }

    public static function tableName()
    {
        return 'view_organizzazioni';
    }
    
    public static function sortModelMap()
    {
        return [
            'contatti' => [
                'telefono_sede',
                'altro_telefono_sede',
                'fax_sede',
                'cellulare_sede',
            ],
            'disponibilita_oraria_sede' => [
                'disponibilita_oraria_sede'
            ],
            'tipo_mezzo' => [
                'ref_tipo_descrizione'
            ],
            'tipo_attrezzatura' => [
                'tipo_attrezzatura_descrizione'
            ],
            'disponibile' => [
                'ref_engaged'
            ],
            'organizzazione' => [
                'denominazione_organizzazione'
            ],
            'id_sede' => [
                'id_sede'
            ]
        ];
    }

    
    public $id_categoria;
    public $id_tipologia;
    public $id_evento;
    public $dist_km;
    public $sort;
    public $sort_order;
    public $distance;
    public $id_utl_automezzo_tipo;
    public $id_utl_attrezzatura_tipo;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_utl_automezzo_tipo','id_utl_attrezzatura_tipo','id_provincia','id_comune','distance','id_evento', 'lat', 'lon','specializzazione', 'sort','sort_order'], 'default', 'value' => null],
            [['id_utl_automezzo_tipo','id_utl_attrezzatura_tipo','id_provincia','id_comune','distance', 'id_evento','specializzazione','num_comunale'], 'integer']
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(),['distance']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_utl_automezzo_tipo' => 'Tipo automezzo',
            'id_utl_attrezzatura_tipo' => 'Tipo attrezzatura',
            'id_provincia' => 'Provincia',
            'id_comune' => 'Comune',
            'distance' => 'Distanza',
            'id_evento' => 'Evento',
            'num_comunale' => 'Numero comunale',
            'id_organizzazione' => 'Organizzazione',
            'sort' => 'Ordina per',
            'sort_order' => 'Direzione',
            'id_categoria' => 'Categoria',
            'id_tipologia' => 'Tipologia'
        ];
    }    

    public function behaviors()
    {
        return [
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POINT,
                'attribute' => 'geom_sede',
            ]
        ];
    }

    public function getSezioneSpecialistica() {
        return $this->hasMany( TblSezioneSpecialistica::className(), ['id' => 'id_sezione_specialistica'])
        ->viaTable('con_organizzazione_sezione_specialistica', ['id_organizzazione'=>'id_organizzazione']);
    }

    public function getContattiAttivazioni() {
        return $this->hasMany( ConOrganizzazioneContatto::className(), ['id_organizzazione' => 'id_organizzazione'])->where(['use_type'=>1]);
    }

    public function getContatti() {
        return $this->hasMany( UtlContatto::className(), ['id' => 'id_contatto'])
        ->via('contattiAttivazioni');
    }
    
}
