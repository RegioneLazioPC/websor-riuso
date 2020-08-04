<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "alm_con_zona_criticita".
 *
 * @property integer $id
 * @property integer $id_allerta
 * @property integer $id_criticita
 * @property integer $zona
 * @property boolean $precipitazioni
 * @property boolean $nevicate
 * @property boolean $venti
 * @property boolean $mareggiate
 * @property boolean $temporali
 * @property boolean $idro
 * @property boolean $fasi_operative
 */
class AlmZonaCriticita extends \yii\db\ActiveRecord
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
        return 'alm_con_zona_criticita';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_allerta', 'id_criticita', 'zona', 'temporali', 'idro', 'fasi_operative'], 'integer'],
            [['precipitazioni', 'nevicate', 'venti', 'mareggiate'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_allerta' => 'Id Allerta',
            'id_criticita' => 'Id Criticita',
            'zona' => 'Zona',
            'precipitazioni' => 'Precipitazioni',
            'nevicate' => 'Nevicate',
            'venti' => 'Venti',
            'mareggiate' => 'Mareggiate',
            'temporali' => 'Temporali',
            'idro' => 'Idro',
            'fasi_operative' => 'Fasi Operative',
        ];
    }
}
