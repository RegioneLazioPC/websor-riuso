<?php

namespace common\models;

use Yii;
use common\models\LocComune;
use common\models\AlmZonaAllerta;

class ConZonaAllertaComune extends \yii\db\ActiveRecord
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
        return 'con_zona_allerta_comune';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_alm_zona_allerta'], 'integer'],
            [['codistat_comune'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codistat_comune' => 'Codice istat comune',
            'id_alm_zona_allerta' => 'Id zona allerta'
        ];
    }

    public function getZonaAllerta() {
        return $this->hasOne( AlmZonaAllerta::className(), ['id' => 'id_alm_zona_allerta']);
    }

    public function getComune() {
        return $this->hasOne(LocComune::className(), ['codistat' => 'codistat_comune']);
    }
}
