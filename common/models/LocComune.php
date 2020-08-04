<?php

namespace common\models;

use common\models\LocProvincia;
use Yii;

use common\models\AlmZonaAllerta;
use common\models\ConZonaAllertaComune;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "loc_comune".
 *
 * @property integer $id
 * @property integer $id_regione
 * @property integer $id_provincia
 * @property string $comune
 */
class LocComune extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loc_comune';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_regione', 'id_provincia', 'comune'], 'required'],
            [['id_regione', 'id_provincia'], 'integer'],
            [['codistat'], 'string'],
            [['comune'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_regione' => 'Id Regione',
            'id_provincia' => 'Id Provincia',
            'comune' => 'Comune',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvincia()
    {
        return $this->hasOne(LocProvincia::className(), ['id' => 'id_provincia']);
    }

    public function getConZoneAllerta() {
        return $this->hasMany( ConZonaAllertaComune::className(), ['codistat_comune' => 'codistat']);
    }

    public function getZoneAllerta() {
        return $this->hasMany( AlmZonaAllerta::className(), ['id' => 'id_alm_zona_allerta'])
        ->via('conZoneAllerta');
    }



    public function search($params)
    {
        $query = LocComune::find()->joinWith(['zoneAllerta'])
        ->where(['id_regione'=>Yii::$app->params['region_filter_id']])
        ->orderBy([
            'comune' => SORT_ASC
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere(['ilike', 'comune', $this->comune]);

        return $dataProvider;
    }

}
