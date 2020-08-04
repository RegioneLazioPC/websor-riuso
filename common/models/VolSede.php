<?php

namespace common\models;

use Yii;
use nanson\postgis\behaviors\GeometryBehavior;
/**
 * This is the model class for table "vol_sede".
 *
 * @property int $id
 * @property int $id_organizzazione
 * @property string $indirizzo
 * @property int $comune
 * @property string $tipo
 * @property string $email
 * @property string $email_pec
 * @property string $telefono
 * @property string $cellulare
 * @property string $altro_telefono
 * @property string $fax
 * @property string $sitoweb
 * @property array $disponibilita_oraria
 * @property double $lat
 * @property double $lon
 *
 * @property UtlAttrezzatura[] $utlAttrezzaturas
 * @property UtlAutomezzo[] $utlAutomezzos
 * @property VolOrganizzazioneUtente[] $volOrganizzazioneUtentes
 * @property VolOrganizzazione $organizzazione
 */
class VolSede extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_sede';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_organizzazione', 'comune', 'indirizzo', 'tipo'], 'required'],
            [['id_organizzazione', 'comune'], 'default', 'value' => null],
            [['id_organizzazione', 'comune'], 'integer'],
            [['indirizzo', 'tipo', 'disponibilita_oraria','cap'], 'string'],
            [['lat', 'lon', 'coord_x', 'coord_y'], 'number'],
            [['email', 'email_pec', 'telefono', 'cellulare', 'altro_telefono', 'fax', 'altro_fax', 'sitoweb','id_sync'], 'string', 'max' => 255],
            [['id_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['id_organizzazione' => 'id']],
            [['id_specializzazione'], 'exist', 'skipOnError' => true, 'targetClass' => UtlSpecializzazione::className(), 'targetAttribute' => ['id_specializzazione' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POINT,
                'attribute' => 'geom',
            ],
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['geom']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_organizzazione' => 'Organizzazione',
            'id_specializzazione' => 'Specializzazione',
            'indirizzo' => 'Indirizzo',
            'comune' => 'Comune',
            'tipo' => 'Tipo',
            'email' => 'Email',
            'email_pec' => 'Email Pec',
            'telefono' => 'Telefono',
            'cellulare' => 'Cellulare',
            'altro_telefono' => 'Telefono H24',
            'fax' => 'Fax',
            'sitoweb' => 'Sitoweb',
            'disponibilita_oraria' => 'Disponibilita Oraria',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'coord_x' => 'Coordinate Rm40 x',
            'coord_y' => 'Coordinate Rm40 y',
            'cap' => 'Cap',
            'altro_fax' => 'Fax h24'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttrezzature()
    {
        return $this->hasMany(UtlAttrezzatura::className(), ['idsede' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'comune']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecializzazione()
    {
        return $this->hasOne(UtlSpecializzazione::className(), ['id' => 'id_specializzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutomezzi()
    {
        return $this->hasMany(UtlAutomezzo::className(), ['idsede' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolOrganizzazioneUtentes()
    {
        return $this->hasMany(VolOrganizzazioneUtente::className(), ['idsede' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'id_organizzazione']);
    }



    /**
     * Inserisci coordinate
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert) {
        
        if($this->lat && $this->lon) $this->geom = [$this->lon, $this->lat];

        return parent::beforeSave($insert);
    }

    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave($insert, $changedAttributes);

        $this->organizzazione->updateZone();
        
    }

    /**
     * Contatti della sede
     * @return [type] [description]
     */
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->via('conContatto');
    }

    public function getConContatto()
    {
        return $this->hasMany(\common\models\organizzazione\ConSedeContatto::className(), ['id_sede'=>'id']);
    }
}
