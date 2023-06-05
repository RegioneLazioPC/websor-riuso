<?php

namespace common\models\reportistica;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class ViewReportInterventiElicotteri extends \yii\db\ActiveRecord
{
    public $data_dal, $data_al, $n_lanci_da, $n_lanci_a;
    public static function tableName()
    {
        return 'view_report_interventi_elicotteri';
    }

    public function attributes() {
        return [
            'data_dal',
            'data_al',
            'n_lanci_da',
            'n_lanci_a',
            'id_evento',
            'num_protocollo',
            'dataora_decollo',
            'dataora_atterraggio',
            'data_attivazione',
            'id_attivazione',
            'tempo_volo',
            'n_lanci',
            'engaged',
            'elicottero',
            'comune',
            'sigla_provincia',
            'id_comune',
            'id_provincia',
            'provincia',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'n_lanci_da',
                    'n_lanci_a',
                    'id_evento',
                    'num_protocollo',
                    'dataora_decollo',
                    'dataora_atterraggio',
                    'data_attivazione',
                    'id_attivazione',
                    'tempo_volo',
                    'n_lanci',
                    'engaged',
                    'elicottero',
                    'comune',
                    'sigla_provincia',
                    'id_provincia',
                    'id_comune',
                    'provincia',
                ], 'safe'],
                [[
                    'data_dal',
                    'data_al'
                ], 'date', 'format'=>'d-m-Y']
        ];
    }

    
    /**
     * 
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        
        $query = ViewReportInterventiElicotteri::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_evento' => $this->id_evento,
            'id_comune' => $this->id_comune,
            'sigla_provincia' => $this->sigla_provincia,
            'elicottero' => $this->elicottero,
        ]);

        
        if($this->data_dal !=""){ 
            $query->andFilterWhere(['>=', 'data_attivazione', Yii::$app->formatter->asDate($this->data_dal, 'php:Y-m-d')]);
        }

        if($this->data_al != ""){
            $query->andFilterWhere(['<=', 'data_attivazione', Yii::$app->formatter->asDate($this->data_al, 'php:Y-m-d')]);
        }
        
        if($this->n_lanci_da !=""){ 
            $query->andFilterWhere(['>=', 'n_lanci', $this->n_lanci_da]);
        }

        if($this->n_lanci_a != ""){
            $query->andFilterWhere(['<=', 'n_lanci', $this->n_lanci_a]);
        }

        return $dataProvider;
    }

    
}
