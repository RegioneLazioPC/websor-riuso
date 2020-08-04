<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_sede_operativa".
 *
 * @property integer $id
 * @property string $nome
 * @property string $indirizzo
 * @property string $comune
 * @property string $tipo
 * @property string $sigla_provincia
 */
class UtlSalaOperativa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_sala_operativa';
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
            [['tipo'], 'string'],
            [['sigla_provincia'], 'safe'],
            [['nome', 'indirizzo', 'comune'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'indirizzo' => 'Indirizzo',
            'comune' => 'Comune',
            'tipo' => 'Tipo',
        ];
    }
}
