<?php

namespace common\models;

use Yii;
use common\models\UtlTipologia;
/**
 * This is the model class for table "utl_categoria_automezzo_attrezzatura".
 *
 * @property int $id
 * @property string $descrizione
 *
 * @property UtlAttrezzatura[] $utlAttrezzaturas
 * @property UtlAutomezzo[] $utlAutomezzos
 */
class UtlCategoriaAutomezzoAttrezzatura extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_categoria_automezzo_attrezzatura';
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
            [['id_tipo_evento'], 'integer'],
            [['descrizione'], 'string', 'max' => 300],
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
            'id_tipo_evento' => 'Tipo evento'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlAttrezzaturas()
    {
        return $this->hasMany(UtlAttrezzatura::className(), ['idcategoria' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlAutomezzos()
    {
        return $this->hasMany(UtlAutomezzo::className(), ['idcategoria' => 'id']);
    }

    public function getTipoEvento()
    {
        return $this->hasOne(UtlTipologia::className(), ['id'=>'id_tipo_evento']);
    }
}
