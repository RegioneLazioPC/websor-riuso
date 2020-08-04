<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RichiestaCanadair;

/**
 * RichiestaCanadairSearch represents the model behind the search form of `common\models\RichiestaCanadair`.
 */
class RichiestaCanadairSearch extends RichiestaCanadair
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idevento', 'idoperatore', 'idcomunicazione'], 'integer'],
            [['created_at'], 'safe'],
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
        $query = RichiestaCanadair::find();

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        
        $query->andFilterWhere([
            'id' => $this->id,
            'idevento' => $this->idevento,
            'idoperatore' => $this->idoperatore,
            'idcomunicazione' => $this->idcomunicazione,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($id, $params)
    {
        $query = RichiestaCanadair::find()
        ->joinWith(['evento'])
        ->where(['or',
            ['idevento' => $id],
            ['utl_evento.idparent' => $id]
        ])
        ->orderBy('created_at DESC');

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

       
        $query->andFilterWhere([
            'id' => $this->id,
            'idevento' => $this->idevento,
            'idoperatore' => $this->idoperatore,
            'idcomunicazione' => $this->idcomunicazione,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
