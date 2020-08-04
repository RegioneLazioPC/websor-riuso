<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_squadra_operativa".
 *
 * @property integer $id
 * @property string $nome
 * @property string $caposquadra
 * @property string $idcomune
 * @property integer $numero_membri
 * @property string $tel_caposquadra
 * @property string $cell_caposquadra
 * @property string $frequenza_tras
 * @property string $frequenza_ric
 *
 * @property LocComune $comune
 */
class UtlSquadraOperativa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_squadra_operativa';
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
            [['nome', 'caposquadra'], 'required'],
            [['numero_membri', 'idcomune'], 'integer'],
            [['nome', 'caposquadra', 'tel_caposquadra', 'cell_caposquadra', 'frequenza_tras', 'frequenza_ric'], 'string', 'max' => 255],
            [['idcomune'], 'exist', 'skipOnError' => true, 'targetClass' => LocComune::className(), 'targetAttribute' => ['comune' => 'comune']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome squadra',
            'caposquadra' => 'Nominativo Caposquadra',
            'idcomune' => 'Comune',
            'numero_membri' => 'Numero Membri',
            'tel_caposquadra' => 'Tel Caposquadra',
            'cell_caposquadra' => 'Cell Caposquadra',
            'frequenza_tras' => 'Frequenza Tras',
            'frequenza_ric' => 'Frequenza Ric',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'idcomune']);
    }
}
