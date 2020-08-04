<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlAutomezzo;

/**
 * UtlAutomezzoSearch represents the model behind the search form of `common\models\UtlAutomezzo`.
 */
class UtlAutomezzoSearch extends UtlAutomezzo
{
    public $org;

    public function attributes() {
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
            [['id', 'idsquadra', 'idtipo', 'idorganizzazione', 'idsede','ref_id'], 'integer'],
            [['meta'], 'string'],
            [['targa', 'data_immatricolazione', 'classe', 'sottoclasse', 'modello', 'disponibilita','org'], 'safe'],
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
        $query = UtlAutomezzo::find()->joinWith(['organizzazione']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        

        $this->load($params);
       

        if (!$this->validate()) {
            return $dataProvider;
        }

        
        $query->andFilterWhere([
            'utl_automezzo.id' => $this->id,
            'data_immatricolazione' => $this->data_immatricolazione,
            'idsquadra' => $this->idsquadra,
            'idtipo' => $this->idtipo,
            'capacita' => $this->capacita,
            'idorganizzazione' => $this->idorganizzazione,
            'idsede' => $this->idsede,
        ]);

        if($this->org) :
            $query->andWhere(['ilike', 'vol_organizzazione.denominazione', $this->org]);
        endif;

        
        $dataProvider->sort->attributes['org'] = [
            'asc' => ['vol_organizzazione.denominazione' => SORT_ASC],
            'desc' => ['vol_organizzazione.denominazione' => SORT_DESC],
        ];

        $query->andFilterWhere(['ilike', 'targa', $this->targa])
            ->andFilterWhere(['ilike', 'classe', $this->classe])
            ->andFilterWhere(['ilike', 'sottoclasse', $this->sottoclasse])
            ->andFilterWhere(['ilike', 'modello', $this->modello])
            ->andFilterWhere(['ilike', 'disponibilita', $this->disponibilita]);


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
