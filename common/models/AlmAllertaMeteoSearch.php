<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AlmAllertaMeteo;

/**
 * AlmAllertaMeteoSearch represents the model behind the search form of `common\models\AlmAllertaMeteo`.
 */
class AlmAllertaMeteoSearch extends AlmAllertaMeteo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'protocollo', 'num_documento'], 'integer'],
            [['data_allerta'], 'date', 'format' => 'php:Y-m-d' ],
            //[['messaggio'], 'safe'],
            [['data_allerta', 'data_creazione', 'data_aggiornamento'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AlmAllertaMeteo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['data_allerta' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'data_allerta' => $this->data_allerta,
            
        ]);

        $query->andFilterWhere(['ilike', 'messaggio', $this->messaggio]);

        return $dataProvider;
    }
}
