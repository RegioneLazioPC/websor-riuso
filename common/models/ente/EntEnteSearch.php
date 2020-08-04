<?php

namespace common\models\ente;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ente\EntEnte;

class EntEnteSearch extends EntEnte
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['denominazione'], 'string'],
            [['zone_allerta'], 'safe'],
            [['update_zona_allerta_strategy'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    
    /**
     * 
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        
        $query = EntEnte::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->zone_allerta)) {
            $cnd = ['and'];
            foreach ($this->zone_allerta as $zona) {
                $cnd[] = ['ilike', 'zone_allerta', $zona ];
            }
            $query->andFilterWhere($cnd);
        }

        if(!empty($this->update_zona_allerta_strategy)) {
            $query->andFilterWhere(['update_zona_allerta_strategy'=>$this->update_zona_allerta_strategy]);
        }

        if($this->getAttribute('denominazione')!=""){
            $query->andFilterWhere(['ilike', 'denominazione', $this->getAttribute('denominazione')]);
        }


        return $dataProvider;
    }

}
