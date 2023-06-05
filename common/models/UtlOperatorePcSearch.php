<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlOperatorePc;

/**
 * UtlOperatorePcSearch represents the model behind the search form about `common\models\UtlOperatorePc`.
 */
class UtlOperatorePcSearch extends UtlOperatorePc
{
    public $sessionOperatore, $nome, $cognome, $matricola, $status;

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idsalaoperativa', 'iduser'], 'integer'],
            [['nome', 'cognome', 'email', 'matricola', 'ruolo', 'sessionOperatore', 'nome', 'cognome', 'matricola', 'status'], 'safe'],
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
        
        $query = UtlOperatorePc::find()->joinWith(['sessionOperatore', 'anagrafica', 'user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['sessionOperatore'] = [
            'asc' => ['session.id_user' => SORT_ASC],
            'desc' => ['session.id_user' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nome'] = [
            'asc' => ['utl_anagrafica.nome' => SORT_ASC],
            'desc' => ['utl_anagrafica.nome' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cognome'] = [
            'asc' => ['utl_anagrafica.cognome' => SORT_ASC],
            'desc' => ['utl_anagrafica.cognome' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['matricola'] = [
            'asc' => ['utl_anagrafica.matricola' => SORT_ASC],
            'desc' => ['utl_anagrafica.matricola' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        
        $query->andFilterWhere([
            'id' => $this->id,
            'idsalaoperativa' => $this->idsalaoperativa,
            'iduser' => $this->iduser
        ]);

        if(!empty($this->nome)) $query->andFilterWhere(['ilike', 'utl_anagrafica.nome', $this->nome]);

        if(!empty($this->status)) {
            if($this->status == -1) $this->status = 0;
            $query->andFilterWhere(['=', 'user.status', $this->status]);
        }

        if(!empty($this->cognome)) $query->andFilterWhere(['ilike', 'utl_anagrafica.cognome', $this->cognome]);

        if(!empty($this->matricola)) $query->andFilterWhere(['ilike', 'utl_anagrafica.matricola', $this->matricola]);
            
        if(!empty($this->ruolo)) $query->andFilterWhere(['utl_operatore_pc.ruolo' => $this->ruolo]);

        if($this->sessionOperatore == '1'){
            $dataLimite = date('Y-m-d H:i:s', strtotime("-1 hour")); 
            $query->andWhere(['not', ['session.id_user' => null]]);
        }elseif($this->sessionOperatore == '0'){
            $query->andWhere(['session.id_user' => null]);
        }
        
        return $dataProvider;
    }
}
