<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlCategoriaAutomezzoAttrezzatura;

/**
 * UtlCategoriaAutomezzoAttrezzaturaSearch represents the model behind the search form of `common\models\UtlCategoriaAutomezzoAttrezzatura`.
 */
class UtlCategoriaAutomezzoAttrezzaturaSearch extends UtlCategoriaAutomezzoAttrezzatura
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_tipo_evento'], 'integer'],
            [['descrizione'], 'safe'],
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
        $query = UtlCategoriaAutomezzoAttrezzatura::find()->joinWith(['tipoEvento']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_tipo_evento' => $this->id_tipo_evento
        ]);

        $dataProvider->sort->attributes['id_tipo_evento'] = [
            'asc' => ['utl_tipologia.tipologia' => SORT_ASC],
            'desc' => ['utl_tipologia.tipologia' => SORT_DESC],
        ];

        $query->andFilterWhere(['ilike', 'descrizione', $this->descrizione]);
        
        return $dataProvider;
    }
}
