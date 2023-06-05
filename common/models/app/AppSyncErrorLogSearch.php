<?php

namespace common\models\app;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\app\AppSyncErrorLog;

/**
 * AppSyncErrorLogSearch represents the model behind the search form of `common\models\AppSyncErrorLog`.
 */
class AppSyncErrorLogSearch extends AppSyncErrorLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level', 'service'], 'string'],
            [['created_at'], 'date', 'format' => 'php:Y-m-d H:i' ]
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AppSyncErrorLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'level' => $this->level,
            'service' => $this->service
        ]);

        if(!empty($this->created_at)) {
            $dt = \DateTime::createFromFormat('Y-m-d H:i', $this->created_at);
            if(!is_bool($dt)) {
                $query->andFilterWhere(['<', 'created_at', $dt->getTimestamp()]);
            }
        }
        return $dataProvider;
    }
}
