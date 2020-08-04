<?php

namespace common\models;

use Yii;

use common\models\UplMedia;
use yii\behaviors\TimestampBehavior;
use nanson\postgis\behaviors\GeometryBehavior;

use yii\db\Expression;
/**
 * This is the model class for table "alm_allerta_meteo".
 *
 * @property integer $id
 * @property integer $protocollo
 * @property integer $num_documento
 * @property integer $cala1
 * @property integer $cala2
 * @property integer $cala3
 * @property integer $cala4
 * @property integer $cala5
 * @property integer $cala6
 * @property integer $cala7
 * @property integer $cala8
 * @property string $data_allerta
 * @property string $messaggio
 * @property boolean $avviso_meteo
 * @property boolean $avviso_idro
 * @property integer $livello_criticita_idro
 * @property string $data_creazione
 * @property string $data_aggiornamento
 * @property string $url_pdf
 *
 * @property AlmTipoAllerta $cala10
 * @property AlmTipoAllerta $cala20
 * @property AlmTipoAllerta $cala30
 * @property AlmTipoAllerta $cala40
 * @property AlmTipoAllerta $cala50
 * @property AlmTipoAllerta $cala60
 * @property AlmTipoAllerta $cala70
 * @property AlmTipoAllerta $cala80
 * @property AlmTipoAllerta $livelloCriticitaIdro
 */
class AlmAllertaMeteo extends \yii\db\ActiveRecord
{
    public $mediaFile, $zone_allerta_array;

    //public $createdAtAttribute = 'data_creazione';
    //public $updatedAtAttribute = 'data_aggiornamento';

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'data_creazione',
                'updatedAtAttribute' => 'data_aggiornamento',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POINT,
                'attribute' => 'geom',
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alm_allerta_meteo';
    }

    public function fields()
    {
        return ['id', 'data_allerta', 'messaggio', 'url_pdf', 'lat', 'lon', 'geom'];
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lat', 'lon'], 'safe'],
            [['protocollo', 'num_documento', 'id_media'], 'integer'],
            [['data_allerta'], 'required'],
            [[ 'data_creazione', 'data_aggiornamento'], 'safe'],
            [['data_allerta'], 'date', 'format' => 'php:Y-m-d' ],
            [['messaggio'], 'string'],
            
            [['avviso_meteo', 'avviso_idro'], 'boolean'],
            [['id_media'], 'exist', 'skipOnError' => false, 'targetClass' => UplMedia::className(), 'targetAttribute' => ['id_media' => 'id']],
            [['zone_allerta_array'], 'safe'],
            [['zone_allerta'],'string'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'protocollo' => 'Protocollo',
            'num_documento' => 'Num Documento',
            'id_media' => 'File',
            'lat' => 'Latitudine',
            'lon' => 'Longitudine',
            'mediaFile' => 'Documento',
            'zone_allerta' => 'Zone di allerta',
            'data_allerta' => 'Data allerta',
            'messaggio' => 'Messaggio',
            'data_creazione' => 'Data Creazione',
            'data_aggiornamento' => 'Data Aggiornamento',
            
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArrayCriticita()
    {
        return [
            1 => ['label'=>'Verde', 'class'=>'allert-green', 'fase_operativa'=>'Base', 'color'=>'#70d66e'],
            2 => ['label'=>'Giallo', 'class'=>'allert-yellow', 'fase_operativa'=>'Preemergenza', 'color'=>'#f4fb35'],
            3 => ['label'=>'Arancione', 'class'=>'allert-orange', 'fase_operativa'=>'Emergenza', 'color'=>'#ffc73c'],
            4 => ['label'=>'Rosso', 'class'=>'allert-red', 'fase_operativa'=>'Allerta', 'color'=>'#ff2d3c']
        ];
    }

    

    public function getFile()
    {
        return $this->hasMany(UplMedia::className(), ['id' => 'id_media'])
        ->viaTable('con_alm_allerta_media', ['id_allerta'=>'id']);
    }

    public function getMessages()
    {
        return $this->hasOne(MasMessage::className(), ['id_allerta' => 'id']);
    }

    /**
     * Inserisci geom field
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert) {
        
        if($this->lat && $this->lon) $this->geom = [$this->lon, $this->lat];
        return parent::beforeSave($insert);
    }
}

