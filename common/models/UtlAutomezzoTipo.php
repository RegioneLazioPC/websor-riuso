<?php

namespace common\models;

use Yii;
use common\models\UtlAggregatoreTipologie;
/**
 * This is the model class for table "utl_automezzo_tipo".
 *
 * @property int $id
 * @property string $descrizione
 *
 * @property UtlAutomezzo[] $utlAutomezzos
 */
class UtlAutomezzoTipo extends \yii\db\ActiveRecord
{
    public $aggregatore;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_automezzo_tipo';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_mezzo_aereo'], 'boolean'],
            [['aggregatore'], 'safe'],
            [['descrizione', 'id_sync'], 'string', 'max' => 300],
            [['descrizione'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descrizione' => 'Descrizione',
            'aggregatore' => 'Tipologie'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlAutomezzos()
    {
        return $this->hasMany(UtlAutomezzo::className(), ['idtipo' => 'id']);
    }

    public function getAggregatori()
    {
        return $this->hasMany(UtlAggregatoreTipologie::className(), ['id'=>'id_aggregatore'])
        ->viaTable('con_aggregatore_tipologie_tipologie', ['id_tipo_automezzo'=>'id']);
    }

    public function getAggregatoriUnique()
    {
        return $this->hasMany(UtlAggregatoreTipologie::className(), ['id'=>'id_aggregatore'])
        ->viaTable('con_aggregatore_tipologie_tipologie as aggregatore_tipologie_automezzo', ['id_tipo_attrezzatura'=>'id']);
    }

}
