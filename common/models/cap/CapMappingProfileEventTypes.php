<?php

namespace common\models\cap;

use Yii;
use common\models\cap\CapResources;
use common\models\cap\ConCapMessageIncident;
use common\models\cap\ConCapMessageReference;
use common\models\UtlTipologia;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class CapMappingProfileEventTypes extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ],
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cap_mapping_profile_event_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tipo_evento', 'id_sottotipo_evento'], 'integer'],
            [['stringa_tipo_evento'],'string'],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
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

    public function getEventType() {
        return $this->hasOne(UtlTipologia::className(), ['id'=>'id_tipo_evento']);
    }

    public function getEventSubType() {
        return $this->hasOne(UtlTipologia::className(), ['id'=>'id_sottotipo_evento']);
    }

    public function search($params)
    {
        $query = CapMappingProfileEventTypes::find();

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
            'id_tipo_evento' => $this->id_tipo_evento,
            'id_sottotipo_evento' => $this->id_sottotipo_evento,
            'stringa_tipo_evento' => $this->stringa_tipo_evento
        ]);

        return $dataProvider;
    }

}
