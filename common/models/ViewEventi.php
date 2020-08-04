<?php

namespace common\models;

use Yii;
use common\models\UtlEvento;
use common\models\UtlIngaggio;

use nanson\postgis\behaviors\GeometryBehavior;
/**
 * This is the model class for table "view_eventi".
 *
 * @property int $id
 * @property int $tipologia_evento
 * @property string $note
 * @property double $lat
 * @property double $lon
 * @property int $idcomune
 * @property string $luogo
 * @property string $direzione
 * @property string $distanza
 * @property string $dataora_evento
 * @property string $dataora_modifica
 * @property string $num_protocollo
 * @property string $indirizzo
 * @property int $sottotipologia_evento
 * @property bool $pericolo
 * @property bool $feriti
 * @property bool $vittime
 * @property bool $interruzione_viabilita
 * @property bool $aiuto_segnalatore
 * @property bool $is_public
 * @property string $geom
 * @property int $idparent
 * @property string $stato
 * @property int $id_tipo_evento
 * @property string $tipologia_tipo_evento
 * @property int $id_sottotipo_evento
 * @property string $tipologia_sottotipo_evento
 * @property string $comune
 */
class ViewEventi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_eventi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tipologia_evento', 'idcomune', 'sottotipologia_evento', 'idparent', 'id_tipo_evento', 'id_sottotipo_evento'], 'default', 'value' => null],
            [['id', 'tipologia_evento', 'idcomune', 'sottotipologia_evento', 'idparent', 'id_tipo_evento', 'id_sottotipo_evento'], 'integer'],
            [['note', 'stato'], 'string'],
            [['lat', 'lon'], 'number'],
            [['dataora_evento', 'dataora_modifica'], 'safe'],
            [['pericolo', 'feriti', 'vittime', 'interruzione_viabilita', 'aiuto_segnalatore', 'is_public'], 'boolean'],
            [['luogo', 'direzione', 'num_protocollo', 'indirizzo', 'tipologia_tipo_evento', 'tipologia_sottotipo_evento', 'comune'], 'string', 'max' => 255],
            [['distanza'], 'string', 'max' => 100],
        ];
    }

    public function fields() 
    {
        return array_merge(
            parent::fields(),[ 'sottoeventi', 'ingaggi']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipologia_evento' => 'Tipologia Evento',
            'note' => 'Note',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'idcomune' => 'Idcomune',
            'luogo' => 'Luogo',
            'direzione' => 'Direzione',
            'distanza' => 'Distanza',
            'dataora_evento' => 'Dataora Evento',
            'dataora_modifica' => 'Dataora Modifica',
            'num_protocollo' => 'Num Protocollo',
            'indirizzo' => 'Indirizzo',
            'sottotipologia_evento' => 'Sottotipologia Evento',
            'pericolo' => 'Pericolo',
            'feriti' => 'Feriti',
            'vittime' => 'Vittime',
            'interruzione_viabilita' => 'Interruzione Viabilita',
            'aiuto_segnalatore' => 'Aiuto Segnalatore',
            'is_public' => 'Is Public',
            'geom' => 'Geom',
            'idparent' => 'Idparent',
            'stato' => 'Stato',
            'id_tipo_evento' => 'Id Tipo Evento',
            'tipologia_tipo_evento' => 'Tipologia Tipo Evento',
            'id_sottotipo_evento' => 'Id Sottotipo Evento',
            'tipologia_sottotipo_evento' => 'Tipologia Sottotipo Evento',
            'comune' => 'Comune',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POINT,
                'attribute' => 'geom',
            ]
        ];
    }

    public function getSottoeventi() {
        return $this->hasMany(UtlEvento::className(), ['idparent' => 'id']);
    }

    public function getIngaggi() {
        return $this->hasMany(UtlIngaggio::className(), ['idevento' => 'id']);
    }
}
