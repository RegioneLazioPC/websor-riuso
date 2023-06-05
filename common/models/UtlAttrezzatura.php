<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_attrezzatura".
 *
 * @property int $id
 * @property int $idcategoria
 * @property int $idtipo
 * @property string $classe
 * @property string $sottoclasse
 * @property string $modello
 * @property double $capacita
 * @property string $unita
 * @property int $idorganizzazione
 * @property int $idsede
 * @property int $idautomezzo
 *
 * @property UtlAttrezzaturaTipo $tipo
 * @property UtlAutomezzo $automezzo
 * @property UtlCategoriaAutomezzoAttrezzatura $categoria
 * @property VolOrganizzazione $organizzazione
 * @property VolSede $sede
 */
class UtlAttrezzatura extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_attrezzatura';
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
            [['idcategoria', 'idtipo', 'idorganizzazione', 'idsede', 'idautomezzo'], 'default', 'value' => null],
            [['idcategoria', 'idtipo', 'idorganizzazione', 'idsede', 'idautomezzo','ref_id','disponibilita'], 'integer'],
            [['engaged'], 'default', 'value' => false],
            [['capacita'], 'number'],
            [['meta'], 'safe'],
            [['classe', 'sottoclasse', 'modello','allestimento'], 'safe'],
            [[ 'id_sync'], 'string', 'max' => 100],
            [['unita','tempo_attivazione'], 'string', 'max' => 255],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAttrezzaturaTipo::className(), 'targetAttribute' => ['idtipo' => 'id']],
            [['idautomezzo'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAutomezzo::className(), 'targetAttribute' => ['idautomezzo' => 'id']],
            [['idcategoria'], 'exist', 'skipOnError' => true, 'targetClass' => UtlCategoriaAutomezzoAttrezzatura::className(), 'targetAttribute' => ['idcategoria' => 'id']],
            [['idorganizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['idorganizzazione' => 'id']],
            [['idsede'], 'exist', 'skipOnError' => true, 'targetClass' => VolSede::className(), 'targetAttribute' => ['idsede' => 'id']],
            [['idtipo', 'idorganizzazione', 'modello'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idcategoria' => 'Categoria',
            'tempo_attivazione' => 'Tempo di attivazione',
            'allestimento' => 'Allestimento',
            'idtipo' => 'Tipo',
            'classe' => 'Classe',
            'sottoclasse' => 'Sottoclasse',
            'modello' => 'Modello',
            'capacita' => 'Capacita',
            'unita' => 'Unita',
            'disponibilita' => 'Disponibilita',
            'idorganizzazione' => 'Organizzazione',
            'idsede' => 'Sede',
            'idautomezzo' => 'Automezzo'            
        ];
    }

    public function extraFields() {
        return ['tipo'];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(UtlAttrezzaturaTipo::className(), ['id' => 'idtipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutomezzo()
    {
        return $this->hasOne(UtlAutomezzo::className(), ['id' => 'idautomezzo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(UtlCategoriaAutomezzoAttrezzatura::className(), ['id' => 'idcategoria']);
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
