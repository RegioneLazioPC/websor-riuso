<?php

namespace api\modules\v1\models;

use common\models\UtlUtente;
use Yii;

/**
 * This is the model class for table "utl_segnalazione".
 *
 * @property integer $id
 * @property integer $idutente
 * @property string $foto
 * @property integer $tipologia_evento
 * @property string $note
 * @property double $lat
 * @property double $lon
 * @property string $direzione
 * @property integer $distanza
 * @property boolean $pericolo
 * @property boolean $feriti
 * @property boolean $interruzione_viabilita
 * @property boolean $aiuto_segnalatore
 *
 * @property UtlUtente $idutente0
 */
class UtlSegnalazione extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_segnalazione';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idutente', 'tipologia_evento', 'distanza'], 'integer'],
            [['note'], 'string'],
            [['lat', 'lon'], 'number'],
            [['pericolo', 'feriti', 'interruzione_viabilita', 'aiuto_segnalatore'], 'boolean'],
            [['foto', 'direzione'], 'string', 'max' => 255],
            [['idutente'], 'exist', 'skipOnError' => true, 'targetClass' => UtlUtente::className(), 'targetAttribute' => ['idutente' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idutente' => 'Idutente',
            'foto' => 'Foto',
            'tipologia_evento' => 'Tipologia Evento',
            'note' => 'Note',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'direzione' => 'Direzione',
            'distanza' => 'Distanza',
            'pericolo' => 'Pericolo',
            'feriti' => 'Feriti',
            'interruzione_viabilita' => 'Interruzione Viabilita',
            'aiuto_segnalatore' => 'Aiuto Segnalatore',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdutente()
    {
        return $this->hasOne(UtlUtente::className(), ['id' => 'idutente']);
    }
}
