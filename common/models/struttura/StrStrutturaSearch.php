<?php

namespace common\models\struttura;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\struttura\StrStruttura;

class StrStrutturaSearch extends StrStruttura
{
       
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['denominazione'], 'string'],
            [['zone_allerta'],'safe'],
            [['update_zona_allerta_strategy'], 'integer']
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
        
        $query = StrStruttura::find();

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

        if($this->getAttribute('denominazione')!=""){
            $query->andFilterWhere(['ilike', 'denominazione', $this->getAttribute('denominazione')]);
        }

        if(!empty($this->update_zona_allerta_strategy)) {
            $query->andFilterWhere(['update_zona_allerta_strategy'=>$this->update_zona_allerta_strategy]);
        }        


        return $dataProvider;
    }

}
