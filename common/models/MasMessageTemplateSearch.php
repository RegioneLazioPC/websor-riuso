<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MasMessageTemplate;

/**
 * MasMessageTemplateSearch represents the model behind the search form of `common\models\MasMessageTemplate`.
 */
class MasMessageTemplateSearch extends MasMessageTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'safe'],
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
        $query = MasMessageTemplate::find();

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere(['ilike', 'nome', $this->nome]);

        return $dataProvider;
    }
}
