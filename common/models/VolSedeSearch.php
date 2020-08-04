<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VolSede;

/**
 * VolSedeSearch represents the model behind the search form of `common\models\VolSede`.
 */
class VolSedeSearch extends VolSede
{
    public $nome_comune;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_organizzazione', 'comune'], 'integer'],
            [['nome_comune'], 'string'],
            [['indirizzo', 'tipo', 'email', 'email_pec', 'telefono', 'cellulare', 'altro_telefono', 'fax', 'sitoweb', 'disponibilita_oraria'], 'safe'],
            [['lat', 'lon'], 'number'],
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
        $query = VolSede::find()->joinWith(['locComune']);

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        
        
        $query->andFilterWhere([
            'id_organizzazione' => $this->id_organizzazione,
            'comune' => $this->comune,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ]);

        $query->andFilterWhere(['ilike', 'indirizzo', $this->indirizzo])
            ->andFilterWhere(['tipo' => $this->tipo])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'email_pec', $this->email_pec])
            ->andFilterWhere(['ilike', 'telefono', $this->telefono])
            ->andFilterWhere(['ilike', 'cellulare', $this->cellulare])
            ->andFilterWhere(['ilike', 'altro_telefono', $this->altro_telefono])
            ->andFilterWhere(['ilike', 'fax', $this->fax])
            ->andFilterWhere(['ilike', 'sitoweb', $this->sitoweb])
            ->andFilterWhere(['ilike', 'disponibilita_oraria', $this->disponibilita_oraria]);

        $dataProvider->sort->attributes['nome_comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        if($this->nome_comune) :
            $query->andWhere(['ilike','loc_comune.comune', $this->nome_comune]);
        endif;

        
        return $dataProvider;
    }
}
