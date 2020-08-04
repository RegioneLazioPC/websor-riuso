<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vol_organizzazione_utente".
 *
 * @property int $id
 * @property int $idutente
 * @property int $idorganizzazione
 * @property int $idsede
 * @property int $idtipo
 *
 * @property UtlUtente $utente
 * @property UtlUtenteTipo $tipo
 * @property VolOrganizzazione $organizzazione
 * @property VolSede $sede
 */
class VolOrganizzazioneUtente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_organizzazione_utente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idutente', 'idorganizzazione', 'idsede', 'idtipo'], 'default', 'value' => null],
            [['idutente', 'idorganizzazione', 'idsede', 'idtipo'], 'integer'],
            [['idutente'], 'exist', 'skipOnError' => true, 'targetClass' => UtlUtente::className(), 'targetAttribute' => ['idutente' => 'id']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => UtlUtenteTipo::className(), 'targetAttribute' => ['idtipo' => 'id']],
            [['idorganizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['idorganizzazione' => 'id']],
            [['idsede'], 'exist', 'skipOnError' => true, 'targetClass' => VolSede::className(), 'targetAttribute' => ['idsede' => 'id']],
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
            'idorganizzazione' => 'Idorganizzazione',
            'idsede' => 'Idsede',
            'idtipo' => 'Idtipo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtente()
    {
        return $this->hasOne(UtlUtente::className(), ['id' => 'idutente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(UtlUtenteTipo::className(), ['id' => 'idtipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'idorganizzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSede()
    {
        return $this->hasOne(VolSede::className(), ['id' => 'idsede']);
    }

}
