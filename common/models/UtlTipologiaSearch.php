<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlTipologia;

/**
 * UtlTipologiaSearch represents the model behind the search form of `common\models\UtlTipologia`.
 */
class UtlTipologiaSearch extends UtlTipologia
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idparent'], 'integer'],
            [['tipologia', 'icon_name'], 'safe'],
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
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UtlTipologia::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'idparent' => $this->idparent,
        ]);

        $query->andFilterWhere(['ilike', 'tipologia', $this->tipologia])
            ->andFilterWhere(['ilike', 'icon_name', $this->icon_name]);

        return $dataProvider;
    }
}
