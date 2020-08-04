<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ConVolontarioIngaggio;

/**
 * ConVolontarioIngaggioSearch represents the model behind the search form of `common\models\ConVolontarioIngaggio`.
 */
class ConVolontarioIngaggioSearch extends ConVolontarioIngaggio
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_volontario', 'id_ingaggio'], 'integer'],
            [['refund'], 'boolean'],
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
        $query = ConVolontarioIngaggio::find()
        ->joinWith(['volontario','volontario.anagrafica'])
        ->orderBy(['utl_anagrafica.cognome' => SORT_ASC, 'utl_anagrafica.nome' => SORT_ASC]);

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
            'id' => $this->id,
            'id_volontario' => $this->id_volontario,
            'id_ingaggio' => $this->id_ingaggio,
            'refund' => $this->refund,
        ]);

        return $dataProvider;
    }
}
