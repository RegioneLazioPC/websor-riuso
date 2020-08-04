<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RichiestaDos;

/**
 * RichiestaDosSearch represents the model behind the search form of `common\models\RichiestaDos`.
 */
class RichiestaDosSearch extends RichiestaDos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idevento', 'idingaggio', 'idoperatore', 'idcomunicazione'], 'integer'],
            [['created_at'], 'safe'],
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
     * 
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = RichiestaDos::find();

        
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
            'idingaggio' => $this->idingaggio,
            'idoperatore' => $this->idoperatore,
            'idcomunicazione' => $this->idcomunicazione,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($id, $params)
    {
        $query = RichiestaDos::find()
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
            'idingaggio' => $this->idingaggio,
            'idoperatore' => $this->idoperatore,
            'idcomunicazione' => $this->idcomunicazione,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
