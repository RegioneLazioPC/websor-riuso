<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RichiestaElicottero;

/**
 * RichiestaElicotteroSearch represents the model behind the search form of `common\models\RichiestaElicottero`.
 */
class RichiestaElicotteroSearch extends RichiestaElicottero
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idevento', 'idingaggio', 'idoperatore', 'priorita_intervento', 'tipo_vegetazione', 'fronte_fuoco_num', 'fronte_fuoco_tot', 'operatori'], 'integer'],
            [['tipo_intervento', 'elettrodotto', 'oreografia', 'vento', 'ostacoli', 'note', 'cfs', 'sigla_radio_dos', 'created_at', 'updated_at'], 'safe'],
            [['area_bruciata', 'area_rischio'], 'number'],
            [['squadre'], 'boolean'],
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
        $query = RichiestaElicottero::find();

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
            'idevento' => $this->idevento,
            'idingaggio' => $this->idingaggio,
            'idoperatore' => $this->idoperatore,
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

        $query->andFilterWhere(['ilike', 'tipo_intervento', $this->tipo_intervento])
            ->andFilterWhere(['ilike', 'elettrodotto', $this->elettrodotto])
            ->andFilterWhere(['ilike', 'oreografia', $this->oreografia])
            ->andFilterWhere(['ilike', 'vento', $this->vento])
            ->andFilterWhere(['ilike', 'ostacoli', $this->ostacoli])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'cfs', $this->cfs])
            ->andFilterWhere(['ilike', 'sigla_radio_dos', $this->sigla_radio_dos]);

        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($id, $params)
    {
        $query = RichiestaElicottero::find()
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

        $query->andFilterWhere(['ilike', 'tipo_intervento', $this->tipo_intervento])
            ->andFilterWhere(['ilike', 'elettrodotto', $this->elettrodotto])
            ->andFilterWhere(['ilike', 'oreografia', $this->oreografia])
            ->andFilterWhere(['ilike', 'vento', $this->vento])
            ->andFilterWhere(['ilike', 'ostacoli', $this->ostacoli])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'cfs', $this->cfs])
            ->andFilterWhere(['ilike', 'sigla_radio_dos', $this->sigla_radio_dos]);

        return $dataProvider;
    }
}
