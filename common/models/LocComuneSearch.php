<?php

namespace common\models;

use Yii;

use common\models\LocComune;
use yii\data\ActiveDataProvider;


class LocComuneSearch extends LocComune
{
    public $zone_allerta;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comune'], 'string'],
            [['zone_allerta'], 'safe']
        ];
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

        if(!empty($this->zone_allerta)) {
            $cnd = ['or'];
            foreach ($this->zone_allerta as $zona) {
                $cnd[] = ['=', 'alm_zona_allerta.code', $zona];
            }
            $query->andFilterWhere($cnd);
        }

        return $dataProvider;
    }

}
