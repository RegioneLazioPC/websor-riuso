<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MasMessage;

/**
 * MasMessageSearch represents the model behind the search form of `common\models\MasMessage`.
 */
class MasMessageSearch extends MasMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_template', 'channel_mail', 'channel_pec', 'channel_push', 'channel_sms', 'channel_fax', 'created_at', 'updated_at'], 'integer'],
            [['note', 'mail_text', 'sms_text', 'push_text', 'fax_text'], 'safe'],
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
        $query = MasMessage::find()->where('id_allerta is null')->orderBy([
            'id' => SORT_DESC //specify sort order ASC for ascending DESC for descending      
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
            'id_template' => $this->id_template,
            'channel_mail' => $this->channel_mail,
            'channel_pec' => $this->channel_pec,
            'channel_push' => $this->channel_push,
            'channel_sms' => $this->channel_sms,
            'channel_fax' => $this->channel_fax,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'mail_text', $this->mail_text])
            ->andFilterWhere(['ilike', 'sms_text', $this->sms_text])
            ->andFilterWhere(['ilike', 'push_text', $this->push_text])
            ->andFilterWhere(['ilike', 'fax_text', $this->fax_text]);

        return $dataProvider;
    }


    
}
