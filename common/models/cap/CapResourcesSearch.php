<?php

namespace common\models\cap;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

use common\models\cap\CapResources;

class CapResourcesSearch extends CapResources
{

    public function rules() {
        return [
            [[
                'identifier',
                'url_feed_rss',
                'url_feed_atom',
                'preferred_feed',
                'profile',
                'raggruppamento',
                'autenticazione',
                'username'
            ], 'string']
        ];
    }

    public function search($params)
    {
        $query = CapResources::find();

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
            'identifier' => $this->identifier,
            'url_feed_rss' => $this->url_feed_rss,
            'url_feed_atom' => $this->url_feed_atom,
            'preferred_feed' => $this->preferred_feed,
            'profile' => $this->profile,
            'raggruppamento' => strtoupper($this->raggruppamento),
            'autenticazione' => $this->autenticazione,
            'username' => $this->username,
        ]);

        return $dataProvider;
    }

}
