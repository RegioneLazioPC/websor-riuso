<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VolTipoOrganizzazione;

/**
 * VolTipoOrganizzazioneSearch represents the model behind the search form of `common\models\VolTipoOrganizzazione`.
 */
class VolTipoOrganizzazioneSearch extends VolTipoOrganizzazione
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['tipologia'], 'safe'],
            [['update_zona_allerta_strategy'], 'integer']
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
        $query = VolTipoOrganizzazione::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if(!empty($this->update_zona_allerta_strategy)) {
            $query->andFilterWhere(['update_zona_allerta_strategy'=>$this->update_zona_allerta_strategy]);
        }

        $query->andFilterWhere(['ilike', 'tipologia', $this->tipologia]);

        
        return $dataProvider;
    }
}
