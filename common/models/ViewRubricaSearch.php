<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewRubrica;

/**
 * ViewRubricaSearch represents the model behind the search form about `common\models\ViewRubrica`.
 */
class ViewRubricaSearch extends ViewRubrica
{
    public $distance, $lat, $lon, $specializzazione;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valore_contatto','tipologia_riferimento','valore_riferimento','comune','provincia','tipo_riferimento'], 'string'],
            [['tipo_contatto','id_riferimento', 'specializzazione'],'integer'],
            [['distance'],'integer'],
            [['lat','lon'],'double']
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
    public function search($params, $paginate = true)
    {
        $query = ViewRubrica::find();

        
        $conf = [
            'query' => $query
        ];
        if(!$paginate) $conf['pagination'] = false;
        $dataProvider = new ActiveDataProvider($conf);



        if(!empty($params['groups'])) {
            $p = [];
            foreach ($params['groups'] as $param) {
                $p[] = intval($param);
            }
            $str = '';
            // in base a canali di invio
            
            if(!empty($params['avaible_tipo_contatto'])) {
                $tipi_contatto = [];
                foreach ($params['avaible_tipo_contatto'] as $tipo) {
                    $tipi_contatto[] = intval($tipo);
                }
                $str .= 'AND tipo_contatto IN (' . implode(',',$tipi_contatto) . ')';
            }
            $query->orWhere(
                'exists (SELECT 1 from con_rubrica_group_contact 
                where 
                CASE 
                  WHEN (tipo_contatto = 2 OR tipo_contatto = 4) THEN check_mobile = 1
                  ELSE 1 = 1
                END AND
                id_rubrica_contatto = id_riferimento and 
                tipo_rubrica_contatto = tipo_riferimento and 
                id_group IN ('.implode(",",$p).') '.$str.'
            )'
            );
        }
        
        if(!empty($params['avaible_tipo_contatto'])) {
            $query->andWhere(['tipo_contatto'=>$params['avaible_tipo_contatto']]);
        }


        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->distance)) {
            
            // filtro in base a distanza
            $query->andWhere('ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) <= :distance')->addParams([
                ':lat' => $this->lat, 
                ':lon' => $this->lon,
                ':distance' => intval($this->distance*1000)
            ]);
        }

        if(!empty($this->specializzazione)) {
            $query->joinWith('specializzazioni')
            ->andWhere(['tbl_sezione_specialistica.id'=>$this->specializzazione]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'valore_contatto' => $this->valore_contatto,
            'tipologia_riferimento' => $this->tipologia_riferimento,
            'tipo_contatto' => $this->tipo_contatto,
            'provincia'=>$this->provincia,
            'id_riferimento'=>$this->id_riferimento,
            'tipo_riferimento'=>$this->tipo_riferimento
        ]);

        $query->andFilterWhere(['ilike', 'valore_riferimento', $this->valore_riferimento]);
        $query->andFilterWhere(['ilike', 'comune', $this->comune]);

        return $dataProvider;
    }

    /**
     * Query per i gruppi
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function searchGroup($params, $paginate = true)
    {
        $query = ViewRubrica::find();

        // add conditions that should always apply here
        $conf = [
            'query' => $query
        ];
        if(!$paginate) $conf['pagination'] = false;
        $dataProvider = new ActiveDataProvider($conf);

        

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'valore_contatto' => $this->valore_contatto,
            'tipologia_riferimento' => $this->tipologia_riferimento,
            'tipo_contatto' => $this->tipo_contatto,
            'provincia'=>$this->provincia
        ]);

        $query->andFilterWhere(['ilike', 'valore_riferimento', $this->valore_riferimento]);
        $query->andFilterWhere(['ilike', 'comune', $this->comune]);

        
        $query->from(['t' => '(SELECT distinct on (id_riferimento, tipo_riferimento) * FROM view_rubrica)']);
        
        if(!empty($this->distance)) {
            
            // filtro in base a distanza
            $query->andWhere('ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) <= :distance')->addParams([
                ':lat' => $this->lat, 
                ':lon' => $this->lon,
                ':distance' => intval($this->distance*1000)
            ]);
        }


        return $dataProvider;
    }
}
