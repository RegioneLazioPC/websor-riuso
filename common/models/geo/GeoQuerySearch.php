<?php

namespace common\models\geo;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\geo\GeoLayer;

class GeoQuerySearch extends GeoQuery
{
       
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'name',
                'layer',
                'group',
                'query_type',
                'result_type',
                'layer_return_field',
            ], 'string'],
            [['id', 'buffer', 'n_geometries', 'result_position'],'integer'],
            [['show_distance', 'enabled'], 'boolean'],
        ];
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        $query = GeoQuery::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'layer' => $this->layer,
            'group' => $this->group,
            'query_type' => $this->query_type,
            'result_type' => $this->result_type,
            'layer_return_field' => $this->layer_return_field,
            'result_position' => $this->result_position,
            'show_distance' => $this->show_distance,
            'enabled' => $this->enabled
        ]);


        if($this->getAttribute('layer_return_field')!=""){
            $query->andFilterWhere(['ilike', 'layer_return_field', $this->layer_return_field]);
        }

        if($this->getAttribute('name')!=""){
            $query->andFilterWhere(['ilike', 'name', $this->name]);
        }

        return $dataProvider;
    }

}
