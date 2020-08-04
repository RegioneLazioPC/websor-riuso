<?php

namespace common\models;

use Yii;
use common\models\UtlAggregatoreTipologie;
/**
 * This is the model class for table "utl_attrezzatura_tipo".
 *
 * @property int $id
 * @property string $descrizione
 *
 * @property UtlAttrezzatura[] $utlAttrezzaturas
 */
class UtlAttrezzaturaTipo extends \yii\db\ActiveRecord
{
    public $aggregatore;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_attrezzatura_tipo';
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
    public function getUtlAttrezzaturas()
    {
        return $this->hasMany(UtlAttrezzatura::className(), ['idtipo' => 'id']);
    }

    public function getAggregatori()
    {
        return $this->hasMany(UtlAggregatoreTipologie::className(), ['id'=>'id_aggregatore'])
        ->viaTable('con_aggregatore_tipologie_tipologie', ['id_tipo_attrezzatura'=>'id']);
    }

    public function getAggregatoriUnique()
    {
        return $this->hasMany(UtlAggregatoreTipologie::className(), ['id'=>'id_aggregatore'])
        ->viaTable('con_aggregatore_tipologie_tipologie as aggregatore_tipologie_attrezzatura', ['id_tipo_attrezzatura'=>'id']);
    }

}
