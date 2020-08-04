<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RichiestaMezzoAereo;

/**
 * RichiestaMezzoAereoSearch represents the model behind the search form about `common\models\RichiestaMezzoAereo`.
 */
class RichiestaMezzoAereoSearch extends RichiestaMezzoAereo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'priorita_intervento', 'tipo_vegetazione', 'fronte_fuoco_num', 'fronte_fuoco_tot', 'squadre', 'operatori'], 'integer'],
            [['tipo_intervento', 'elettrodotto', 'oreografia', 'vento', 'ostacoli', 'note', 'cfs', 'sigla_radio_dos', 'created_at', 'updated_at'], 'safe'],
            [['area_bruciata', 'area_rischio'], 'number'],
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
        $query = RichiestaMezzoAereo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'priorita_intervento' => $this->priorita_intervento,
            'tipo_vegetazione' => $this->tipo_vegetazione,
            'area_bruciata' => $this->area_bruciata,
            'area_rischio' => $this->area_rischio,
            'fronte_fuoco_num' => $this->fronte_fuoco_num,
            'fronte_fuoco_tot' => $this->fronte_fuoco_tot,
            'squadre' => $this->squadre,
            'operatori' => $this->operatori,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'tipo_intervento', $this->tipo_intervento])
            ->andFilterWhere(['like', 'elettrodotto', $this->elettrodotto])
            ->andFilterWhere(['like', 'oreografia', $this->oreografia])
            ->andFilterWhere(['like', 'vento', $this->vento])
            ->andFilterWhere(['like', 'ostacoli', $this->ostacoli])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'cfs', $this->cfs])
            ->andFilterWhere(['like', 'sigla_radio_dos', $this->sigla_radio_dos]);

        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($id, $params)
    {
        $query = RichiestaMezzoAereo::find()
        ->joinWith(['evento'])
        ->where(['or',
            ['idevento' => $id],
            ['utl_evento.idparent' => $id]
        ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'priorita_intervento' => $this->priorita_intervento,
            'tipo_vegetazione' => $this->tipo_vegetazione,
            'area_bruciata' => $this->area_bruciata,
            'area_rischio' => $this->area_rischio,
            'fronte_fuoco_num' => $this->fronte_fuoco_num,
            'fronte_fuoco_tot' => $this->fronte_fuoco_tot,
            'squadre' => $this->squadre,
            'operatori' => $this->operatori,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'tipo_intervento', $this->tipo_intervento])
            ->andFilterWhere(['like', 'elettrodotto', $this->elettrodotto])
            ->andFilterWhere(['like', 'oreografia', $this->oreografia])
            ->andFilterWhere(['like', 'vento', $this->vento])
            ->andFilterWhere(['like', 'ostacoli', $this->ostacoli])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'cfs', $this->cfs])
            ->andFilterWhere(['like', 'sigla_radio_dos', $this->sigla_radio_dos]);

        return $dataProvider;
    }
}
