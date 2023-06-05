<?php

namespace common\models\cap;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\cap\CapConsumer;


class CapConsumerSearch extends CapConsumer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'string'],
            [['address'],'email'],
            [['id'], 'integer']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

   
    public function search($params)
    {
        $query = CapConsumer::find()->orderBy([
            'id' => SORT_DESC 
        ]);

        // add conditions that should always apply here

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
            'address' => $this->address,
            'username' => $this->username,
        ]);


        return $dataProvider;
    }
}
