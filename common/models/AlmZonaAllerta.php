<?php

namespace common\models;

use Yii;
use common\models\LocComune;
use common\models\ConZonaAllertaComune;

class AlmZonaAllerta extends \yii\db\ActiveRecord
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
        return 'alm_zona_allerta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome', 'code'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome zona'
        ];
    }

    public function getConComuni() {
        return $this->hasMany( ConZonaAllertaComune::className(), ['id_zona_allerta'=>'id']);
    }

    public function getComuni() {
        return $this->hasMany(LocComune::className(), ['codistat' => 'codistat_comune'])
        ->via('conComuni');
    }
}
