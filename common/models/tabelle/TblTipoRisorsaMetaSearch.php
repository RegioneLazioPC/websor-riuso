<?php

namespace common\models\tabelle;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\tabelle\TblTipoRisorsaMeta;


class TblTipoRisorsaMetaSearch extends TblTipoRisorsaMeta
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'show_in_column'], 'integer'],
            [
                [
                    'key',
                    'ref_id',
                    'label',
                    'id_sync'
                ], 
                'string'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    
    public function search($params)
    {
        $query = TblTipoRisorsaMeta::find()->orderBy([
            'label' => SORT_ASC
        ]);

        
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
            'type' => $this->type,
            'show_in_column' => $this->show_in_column,
            'extra' => $this->extra,
            'key' => $this->key,
            'ref_id' => $this->ref_id,
            'id_sync' => $this->id_sync,
        ]);

        $query->andFilterWhere(['ilike', 'label', $this->label]);

        return $dataProvider;
    }
}
