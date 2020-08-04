<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlAttrezzatura;
use common\models\tabelle\TblTipoRisorsaMeta;

/**
 * UtlAttrezzaturaSearch represents the model behind the search form of `common\models\UtlAttrezzatura`.
 */
class UtlAttrezzaturaSearch extends UtlAttrezzatura
{
    public $org;

    public function attributes() {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            'org'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idtipo', 'idorganizzazione', 'idsede', 'idautomezzo','ref_id'], 'integer'],
            [['classe', 'sottoclasse', 'modello', 'unita', 'org'], 'safe'],
            [['capacita'], 'number'],
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
        $query = UtlAttrezzatura::find()->joinWith(['organizzazione']);

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }

        if($this->org) :
            $query->andWhere(['ilike', 'vol_organizzazione.denominazione', $this->org]);
        endif;

        
        $query->andFilterWhere([
            'utl_attrezzatura.id' => $this->id,
            'utl_attrezzatura.idtipo' => $this->idtipo,
            'utl_attrezzatura.capacita' => $this->capacita,
            'utl_attrezzatura.idorganizzazione' => $this->idorganizzazione,
            'utl_attrezzatura.idsede' => $this->idsede,
            'utl_attrezzatura.idautomezzo' => $this->idautomezzo,
        ]);

        
        $dataProvider->sort->attributes['org'] = [
            'asc' => ['vol_organizzazione.denominazione' => SORT_ASC],
            'desc' => ['vol_organizzazione.denominazione' => SORT_DESC],
        ];

        $query->andFilterWhere(['ilike', 'classe', $this->classe])
            ->andFilterWhere(['ilike', 'sottoclasse', $this->sottoclasse])
            ->andFilterWhere(['ilike', 'modello', $this->modello])
            ->andFilterWhere(['ilike', 'unita', $this->unita]);

        /**
         * Filtriamo i json
         */
        $array_filters = [];
        if(!empty(Yii::$app->request->get('meta')) ) {
            foreach (Yii::$app->request->get('meta') as $meta_key => $meta_filter) {
                if(!empty($meta_filter)) $array_filters[$meta_key] = $meta_filter;
            }

            if(!empty($array_filters)) {
                foreach ($array_filters as $key => $value) {
                    $kk = ':'.$key.'_key';
                    $vv = ':'.$key.'_value';
                    $query->andWhere('meta ->> '.$kk.' = '.$vv.'')
                    ->addParams([
                        $kk => $key,
                        $vv => $value
                    ]);
                }
                
            }
        }

        return $dataProvider;
    }
}
