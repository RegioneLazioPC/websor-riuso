<?php

namespace common\models\geo;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\geo\GeoLayer;

class GeoLayerSearch extends GeoLayer
{
       
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['layer_name', 'geometry_type', 'table_name', 'geometry_column'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        
        $query = GeoLayer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        

        if (!$this->validate()) {
            return $dataProvider;
        }


        if($this->getAttribute('layer_name')!=""){
            $query->andFilterWhere(['ilike', 'layer_name', $this->layer_name]);
        }

        if($this->getAttribute('tabel_name')!=""){
            $query->andFilterWhere(['ilike', 'tabel_name', $this->tabel_name]);
        }

        if($this->getAttribute('geometry_column')!=""){
            $query->andFilterWhere(['ilike', 'geometry_column', $this->geometry_column]);
        }

        if($this->getAttribute('geometry_type')!=""){
            $query->andFilterWhere(['ilike', 'geometry_type', $this->geometry_type]);
        }

        return $dataProvider;
    }

}
