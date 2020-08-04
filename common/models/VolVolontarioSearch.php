<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VolVolontario;

/**
 * VolVolontarioSearch represents the model behind the search form of `\common\models\VolVolontario`.
 */
class VolVolontarioSearch extends VolVolontario
{
    public function attributes() {
        return array_merge(parent::attributes(), [
            'anagrafica.nome','anagrafica.cognome', 'anagrafica.codfiscale', 'organizzazione.denominazione' 
        ]);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_anagrafica', 'id_organizzazione', 'id_sede', 'id_user'], 'integer'],
            [['ruolo', 'spec_principale', 'valido_dal', 'valido_al', 'anagrafica.nome', 'anagrafica.cognome', 'anagrafica.codfiscale', 'organizzazione.denominazione'], 'safe'],
            [['operativo'], 'boolean'],
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
        $query = VolVolontario::find()->joinWith(['anagrafica', 'organizzazione']);

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        

        $query->andFilterWhere(['ilike', 'vol_organizzazione.denominazione', $this->getAttribute('organizzazione.denominazione')]);
        $query->andFilterWhere(['ilike', 'utl_anagrafica.nome', $this->getAttribute('anagrafica.nome')]);
        $query->andFilterWhere(['ilike', 'utl_anagrafica.cognome', $this->getAttribute('anagrafica.cognome')]);
        $query->andFilterWhere(['ilike', 'utl_anagrafica.codfiscale', $this->getAttribute('anagrafica.codfiscale')]);

        
        $query->andFilterWhere([
            'id' => $this->id,
            'valido_dal' => $this->valido_dal,
            'valido_al' => $this->valido_al,
            'operativo' => $this->operativo,
            'id_organizzazione' => $this->id_organizzazione,
            'id_sede' => $this->id_sede,
            'id_user' => $this->id_user,
        ]);

        $query->andFilterWhere(['ilike', 'ruolo', $this->ruolo])
            ->andFilterWhere(['ilike', 'spec_principale', $this->spec_principale]);

        return $dataProvider;
    }
}
