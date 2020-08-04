<?php

namespace common\models;

use Yii;
use common\models\UtlEvento;

use nanson\postgis\behaviors\GeometryBehavior;
/**
 * This is the model class for table "view_segnalazioni".
 *
 * @property int $id
 * @property int $idutente
 * @property int $idsalaoperativa
 * @property string $foto
 * @property int $tipologia_evento
 * @property string $note
 * @property double $lat
 * @property double $lon
 * @property int $idcomune
 * @property string $indirizzo
 * @property string $luogo
 * @property string $direzione
 * @property string $distanza
 * @property string $dataora_segnalazione
 * @property string $stato
 * @property string $fonte
 * @property string $num_protocollo
 * @property bool $foto_locale
 * @property bool $pericolo
 * @property bool $feriti
 * @property bool $vittime
 * @property bool $interruzione_viabilita
 * @property bool $aiuto_segnalatore
 * @property string $geom
 * @property int $id_tipo_segnalazione
 * @property string $tipologia_tipo_segnalazione
 * @property string $comune
 * @property string $nome
 * @property string $cognome
 * @property string $codfiscale
 * @property string $email
 * @property string $matricola
 */
class ViewSegnalazioni extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_segnalazioni';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idutente', 'idsalaoperativa', 'tipologia_evento', 'idcomune', 'id_tipo_segnalazione'], 'default', 'value' => null],
            [['id', 'idutente', 'idsalaoperativa', 'tipologia_evento', 'idcomune', 'id_tipo_segnalazione'], 'integer'],
            [['note', 'stato', 'fonte', 'geom'], 'string'],
            [['lat', 'lon'], 'number'],
            [['dataora_segnalazione'], 'safe'],
            [['media_id','media_orientation','media_tipo'], 'safe'],
            [['foto_locale', 'pericolo', 'feriti', 'vittime', 'interruzione_viabilita', 'aiuto_segnalatore'], 'boolean'],
            [['foto', 'indirizzo', 'luogo', 'direzione', 'distanza', 'num_protocollo', 'tipologia_tipo_segnalazione', 'comune', 'nome', 'cognome', 'matricola'], 'string', 'max' => 255],
            [['codfiscale'], 'string', 'max' => 16],
            [['email'], 'string', 'max' => 355],
        ];
    }

    public function fields() 
    {
        return array_merge(
            parent::fields(),[ 'evento']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idutente' => 'Idutente',
            'idsalaoperativa' => 'Idsalaoperativa',
            'foto' => 'Foto',
            'tipologia_evento' => 'Tipologia Evento',
            'note' => 'Note',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'idcomune' => 'Idcomune',
            'indirizzo' => 'Indirizzo',
            'luogo' => 'Luogo',
            'direzione' => 'Direzione',
            'distanza' => 'Distanza',
            'dataora_segnalazione' => 'Dataora Segnalazione',
            'stato' => 'Stato',
            'fonte' => 'Fonte',
            'num_protocollo' => 'Num Protocollo',
            'foto_locale' => 'Foto Locale',
            'pericolo' => 'Pericolo',
            'feriti' => 'Feriti',
            'vittime' => 'Vittime',
            'interruzione_viabilita' => 'Interruzione Viabilita',
            'aiuto_segnalatore' => 'Aiuto Segnalatore',
            'geom' => 'Geom',
            'id_tipo_segnalazione' => 'Id Tipo Segnalazione',
            'tipologia_tipo_segnalazione' => 'Tipologia Tipo Segnalazione',
            'comune' => 'Comune',
            'nome' => 'Nome',
            'cognome' => 'Cognome',
            'codfiscale' => 'Codfiscale',
            'email' => 'Email',
            'matricola' => 'Matricola',
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

    public function getEvento() {

        return $this->hasOne(ViewEventi::className(), ['id'=>'idevento'])
        ->viaTable('con_evento_segnalazione', ['idsegnalazione'=>'id']);
        
    }
}
