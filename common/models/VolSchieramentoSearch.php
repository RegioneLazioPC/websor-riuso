<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VolSchieramentoSearch represents the model behind the search form about `common\models\VolSchieramentoSearch`.
 */
class VolSchieramentoSearch extends VolSchieramento
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['descrizione'], 'string'],
            [['data_validita'], 'date', 'format'=>'php:Y-m-d']
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
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = VolSchieramento::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data_validita'=>$this->data_validita
        ]);

        $query->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }
}
