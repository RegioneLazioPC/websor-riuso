<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IspIspezione;

/**
 * IspIspezioneSearch represents the model behind the search form about `common\models\IspIspezione`.
 */
class IspIspezioneSearch extends IspIspezione
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idoperatore', 'idtipologia', 'idcomune'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['indirizzo', 'tipo_attivita'], 'safe'],
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
        $query = IspIspezione::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'idoperatore' => $this->idoperatore,
            'idtipologia' => $this->idtipologia,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'idcomune' => $this->idcomune,
        ]);

        $query->andFilterWhere(['like', 'indirizzo', $this->indirizzo])
            ->andFilterWhere(['like', 'tipo_attivita', $this->tipo_attivita]);

        return $dataProvider;
    }
}
