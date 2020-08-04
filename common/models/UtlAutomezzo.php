<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_automezzo".
 *
 * @property int $id
 * @property string $targa
 * @property string $data_immatricolazione
 * @property int $idsquadra
 * @property string $classe
 * @property string $sottoclasse
 * @property string $modello
 * @property int $idcategoria
 * @property int $idtipo
 * @property double $capacita
 * @property string $disponibilita
 * @property int $idorganizzazione
 * @property int $idsede
 *
 * @property UtlAttrezzatura[] $utlAttrezzaturas
 * @property UtlAutomezzoTipo $tipo
 * @property UtlCategoriaAutomezzoAttrezzatura $categoria
 * @property UtlSquadraOperativa $squadra
 * @property VolOrganizzazione $organizzazione
 * @property VolSede $sede
 */
class UtlAutomezzo extends \yii\db\ActiveRecord
{
        /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_automezzo';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['meta']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_immatricolazione','org'], 'safe'],
            [['engaged'], 'default', 'value' => false],
            [['idsquadra', 'idtipo', 'idorganizzazione', 'idsede'], 'default', 'value' => null],
            [['idsquadra', 'idtipo', 'idorganizzazione', 'idsede','ref_id'], 'integer'],
            [['capacita'], 'number'],
            [['meta'], 'safe'],
            [['targa'], 'string', 'max' => 45],
            [['classe', 'sottoclasse', 'modello', 'id_sync'], 'string', 'max' => 100],
            [['disponibilita','tempo_attivazione','allestimento'], 'string', 'max' => 255],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAutomezzoTipo::className(), 'targetAttribute' => ['idtipo' => 'id']],
            [['idsquadra'], 'exist', 'skipOnError' => true, 'targetClass' => UtlSquadraOperativa::className(), 'targetAttribute' => ['idsquadra' => 'id']],
            [['idorganizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['idorganizzazione' => 'id']],
            [['idsede'], 'exist', 'skipOnError' => true, 'targetClass' => VolSede::className(), 'targetAttribute' => ['idsede' => 'id']],
            [['idtipo', 'idorganizzazione','targa'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'targa' => 'Targa',
            'tempo_attivazione' => 'Tempo di attivazione',
            'allestimento' => 'Allestimento',
            'data_immatricolazione' => 'Data immatricolazione',
            'idsquadra' => 'Squadra',
            'classe' => 'Classe',
            'sottoclasse' => 'Sottoclasse',
            'modello' => 'Modello',
            'idtipo' => 'Tipo',
            'capacita' => 'Capacita',
            'disponibilita' => 'Disponibilita',
            'idorganizzazione' => 'Organizzazione',
            'idsede' => 'Sede',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlAttrezzaturas()
    {
        return $this->hasMany(UtlAttrezzatura::className(), ['idautomezzo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(UtlAutomezzoTipo::className(), ['id' => 'idtipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSquadra()
    {
        return $this->hasOne(UtlSquadraOperativa::className(), ['id' => 'idsquadra']);
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
