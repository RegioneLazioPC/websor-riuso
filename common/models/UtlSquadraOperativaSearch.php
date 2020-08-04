<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlSquadraOperativa;

/**
 * UtlSquadraOperativaSearch represents the model behind the search form about `common\models\UtlSquadraOperativa`.
 */
class UtlSquadraOperativaSearch extends UtlSquadraOperativa
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'numero_membri'], 'integer'],
            [['nome', 'caposquadra', 'comune', 'tel_caposquadra', 'cell_caposquadra', 'frequenza_tras', 'frequenza_ric'], 'safe'],
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
        $query = UtlSquadraOperativa::find();

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'numero_membri' => $this->numero_membri,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'caposquadra', $this->caposquadra])
            ->andFilterWhere(['like', 'comune', $this->comune])
            ->andFilterWhere(['like', 'tel_caposquadra', $this->tel_caposquadra])
            ->andFilterWhere(['like', 'cell_caposquadra', $this->cell_caposquadra])
            ->andFilterWhere(['like', 'frequenza_tras', $this->frequenza_tras])
            ->andFilterWhere(['like', 'frequenza_ric', $this->frequenza_ric]);

        return $dataProvider;
    }
}
