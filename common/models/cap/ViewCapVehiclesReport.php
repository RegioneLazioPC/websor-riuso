<?php

namespace common\models\cap;

use yii\data\ActiveDataProvider;
use common\models\UtlSegnalazione;
use Yii;

class ViewCapVehiclesReport extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [

        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_cap_vehicles_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['incident', 'organizzazione', 'targa'], 'string'],
            [[
                'data_attivazione',
                'data_arrivo',
                'data_chiusura',
                'data_deviazione'
            ], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }


    public function search($params, $paginate = true)
    {
        $query = ViewCapVehiclesReport::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['data_attivazione' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['ilike', 'targa', $this->targa]);
        $query->andFilterWhere(['ilike', 'concat(numero_elenco_territoriale, \' - \', organizzazione)', $this->organizzazione]);
        $query->andFilterWhere(['ilike', 'incident', $this->incident]);
        $query->andFilterWhere(['ilike', 'targa', $this->targa]);

        if (!empty($this->data_attivazione)) {
            $query->andFilterWhere(['>=', 'data_attivazione', $this->data_attivazione . "T00:00:01"]);
            $query->andFilterWhere(['<=', 'data_attivazione', $this->data_attivazione . "T23:59:59"]);
            $query->andWhere('data_attivazione is not null');
            $query->andWhere('data_attivazione <> \'\'');
        }

        if (!empty($this->data_arrivo)) {
            $query->andFilterWhere(['>=', 'data_arrivo', $this->data_arrivo . "T00:00:01"]);
            $query->andFilterWhere(['<=', 'data_arrivo', $this->data_arrivo . "T23:59:59"]);
            $query->andWhere('data_arrivo is not null');
            $query->andWhere('data_arrivo <> \'\'');
        }

        if (!empty($this->data_chiusura)) {
            $query->andFilterWhere(['>=', 'data_chiusura', $this->data_chiusura . "T00:00:01"]);
            $query->andFilterWhere(['<=', 'data_chiusura', $this->data_chiusura . "T23:59:59"]);
            $query->andWhere('data_chiusura is not null');
            $query->andWhere('data_chiusura <> \'\'');
        }

        if (!empty($this->data_deviazione)) {
            $query->andFilterWhere(['>=', 'data_deviazione', $this->data_deviazione . "T00:00:01"]);
            $query->andFilterWhere(['<=', 'data_deviazione', $this->data_deviazione . "T23:59:59"]);
            $query->andWhere('data_deviazione is not null');
            $query->andWhere('data_deviazione <> \'\'');
        }


        return $dataProvider;
    }

    public function getSegnalazioni()
    {
        return $this->hasMany(UtlSegnalazione::className(), ['cap_message_identifier'=>'identifier']);
    }
}
