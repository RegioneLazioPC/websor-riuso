<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlAggregatoreTipologie;

/**
 * UtlAggregatoreTipologieSearch represents the model behind the search form of `common\models\UtlAggregatoreTipologie`.
 */
class UtlAggregatoreTipologieSearch extends UtlAggregatoreTipologie
{
    public $id_categoria;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_categoria'], 'integer'],
            [['descrizione'], 'safe'],
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
        $query = UtlAggregatoreTipologie::find()->joinWith(['categoria']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_categoria' => $this->id_categoria
        ]);

        $dataProvider->sort->attributes['id_categoria'] = [
            'asc'  => ['utl_categoria_automezzo_attrezzatura.descrizione' => SORT_ASC],
            'desc' => ['utl_categoria_automezzo_attrezzatura.descrizione' => SORT_DESC],
        ];

        $query->andFilterWhere(['ilike', 'descrizione', $this->descrizione]);
        

        return $dataProvider;
    }
}
