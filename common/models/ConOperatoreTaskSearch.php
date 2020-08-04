<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * ConOperatoreTaskSearch represents the model behind the search form about `common\models\ConOperatoreTask`.
 */
class ConOperatoreTaskSearch extends ConOperatoreTask
{

    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            'funzioneSupporto.descrizione', 'task.descrizione', 'data_dal', 'data_al'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['id', 'idoperatore', 'idevento', 'idfunzione_supporto', 'idtask', 'idsquadra', 'idautomezzo'], 'integer'],
            [['dataora', 'operatore', 'funzioneSupporto.descrizione','task.descrizione', 'data_dal', 'data_al'], 'safe'],
            [['is_task'], 'boolean'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($idEvento, $params)
    {
        
        
        $query = ConOperatoreTask::find()
                                ->joinWith(['operatore', 'task', 'funzioneSupporto','evento'])
                                ->where(['or',
                                    ['idevento' => $idEvento],
                                    ['utl_evento.idparent' => $idEvento]
                                ])
                                ->orderBy('dataora DESC');
                                

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->sort->attributes['operatore'] = [
            'asc' => ['utl_operatore_pc.nome' => SORT_ASC],
            'desc' => ['utl_operatore_pc.nome' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['funzioneSupporto.descrizione'] = [
            'asc' => ['utl_funzioni_supporto.descrizione' => SORT_ASC],
            'desc' => ['utl_funzioni_supporto.descrizione' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['task.descrizione'] = [
            'asc' => ['utl_task.descrizione' => SORT_ASC],
            'desc' => ['utl_task.descrizione' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if($this->getAttribute('data_dal')!=""){
            $query->andFilterWhere(['>=', 'dataora', Yii::$app->formatter->asDate($this->getAttribute('data_dal'), 'php:Y-m-d')]);
        }
        if($this->getAttribute('dataora')!=""){
            $query->andFilterWhere(['>=', 'dataora', Yii::$app->formatter->asDate($this->getAttribute('dataora'), 'php:Y-m-d')]);
        }

        if($this->getAttribute('data_al')!=""){
            $query->andFilterWhere(['<=', 'dataora', Yii::$app->formatter->asDate($this->getAttribute('data_al'), 'php:Y-m-d')]);
        }


        $t = $query->createCommand()->getRawSql();
        return $dataProvider;
    }
}
