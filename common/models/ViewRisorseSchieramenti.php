<?php

namespace common\models;

use Yii;

use yii\data\ActiveDataProvider;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlIngaggio;

use common\models\organizzazione\ConOrganizzazioneContatto;
use common\models\VolOrganizzazione;

class ViewRisorseSchieramenti extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_risorse_schieramenti';
    }

    public static function primaryKey()
    {
        return ["uid"];
    }

    public function rules()
    {
        return [
            [['tipo', 'identifier', 'idtipo', 'organizzazione','schieramento'], 'string'],
            [['num_elenco_territoriale'], 'integer'],
            [['meta'],'safe']
        ];
    }

    public function search($params)
    {
        $query = ViewRisorseSchieramenti::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'num_elenco_territoriale'=>$this->num_elenco_territoriale,
            'tipo' => $this->tipo
        ]);

        $query->andFilterWhere(['ilike', 'identifier', $this->identifier])
            ->andFilterWhere(['ilike', 'organizzazione', $this->organizzazione])
            ->andFilterWhere(['ilike', 'schieramento', $this->schieramento]);

        if(!empty($this->idtipo)) {
            $e = explode("_", $this->idtipo);
            if(count($e) == 2) {
                $query->andFilterWhere(['idtipo'=>$e[1], 'tipo'=>$e[0]]);
            }
        }

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
                    $query->andWhere('_meta ->> '.$kk.' = '.$vv.'')
                    ->addParams([
                        $kk => $key,
                        $vv => $value
                    ]);
                }
                
            }
        }

        return $dataProvider;
    }

    public function getTipoMezzo() {
    	return $this->hasOne(UtlAutomezzoTipo::className(), ['id'=>'idtipo']);
    }

    public function getTipoAttrezzatura() {
    	return $this->hasOne(UtlAttrezzaturaTipo::className(), ['id'=>'idtipo']);
    }

    public function getIngaggioMezzo() {
    	return $this->hasOne(UtlIngaggio::className(), ['idautomezzo'=>'id'])
    	->andOnCondition(['stato'=>[0,1]]);
    }

    public function getIngaggioAttrezzatura() {
    	return $this->hasOne(UtlIngaggio::className(), ['idattrezzatura'=>'id'])
    	->andOnCondition(['stato'=>[0,1]]);
    }

    public function getOrganizzazione() {
    	return $this->hasOne(VolOrganizzazione::className(), ['id'=>'id_organizzazione']);
    }

    public function getSede() {
    	return $this->hasOne(VolSede::className(), ['id'=>'id_sede']);
    }

    public function getContattiAttivazioni() {
        return $this->hasMany( ConOrganizzazioneContatto::className(), ['id_organizzazione' => 'id_organizzazione'])
        ->where(['use_type'=>1]);
    }
}
