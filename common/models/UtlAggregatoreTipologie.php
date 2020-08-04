<?php

namespace common\models;

use Yii;
use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzo;
/**
 * This is the model class for table "utl_aggregatore_tipologie".
 *
 * @property int $id
 * @property string $descrizione
 *
 * @property ConAggregatoreTipologieTipologie[] $conAggregatoreTipologieTipologies
 */
class UtlAggregatoreTipologie extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
        return 'utl_aggregatore_tipologie';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_categoria'], 'integer'],
            [['descrizione'], 'string', 'max' => 255],
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
            'id_categoria' => 'Categoria'
        ];
    }

    public function getCategoria()
    {
        return $this->hasOne(UtlCategoriaAutomezzoAttrezzatura::className(), ['id'=>'id_categoria']);
    }

    public function getTipiAutomezzo()
    {
        return $this->hasMany(UtlAutomezzoTipo::className(), ['id'=>'id_tipo_automezzo'])
        ->viaTable('con_aggregatore_tipologie_tipologie', ['id_aggregatore'=>'id']);
    }

    public function getTipiAttrezzatura()
    {
        return $this->hasMany(UtlAttrezzaturaTipo::className(), ['id'=>'id_tipo_attrezzatura'])
        ->viaTable('con_aggregatore_tipologie_tipologie', ['id_aggregatore'=>'id']);
    }
}
