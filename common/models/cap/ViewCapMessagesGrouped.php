<?php

namespace common\models\cap;

use Yii;
use yii\data\ActiveDataProvider;

class ViewCapMessagesGrouped extends \yii\db\ActiveRecord
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
        return 'view_cap_messages_grouped';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'raggruppamento',
                'identifier',
                'risorsa',
                'incident',
                'event',
                'type',
                'segnalatore',
                'status',
                'sender',
                'sender_name',
                'event_type',
                'event_subtype',
                'expires_rome_timezone',
                
                'profile',
                'code_int',
                'code_call',
                'string_comune',
                'string_provincia',
                'formatted_status',
            ], 'string'],
            [['id', 'major_event'],'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'identifier' => 'Identificatore',
            'sent_rome_timezone' => 'Aggiornamento',
            'call_time' => 'Chiamata',
            'intervent_time' => 'Intervento',
            'arrival_time' => 'Arrivo sul posto',
            'close_time' => 'Chiusura',
            'expires_rome_timezone' => 'Scadenza',
            'event_type' => 'Tipo evento',
            'event_subtype' => 'Sottotipo evento',
            'event' => 'Evento',
            'sender'=>'Mittente',
            'sender_name' => 'Nome mittente',
            'type' => 'Tipo',
            'profile' => 'Profilo',
            'code_int' => 'CODEINT',
            'code_call' => 'CODECALL',
            'string_comune' => 'Comune',
            'string_provincia' => 'Provincia',
            'formatted_status' => 'Stato'
        ];
    }

    public function search($params)
    {
        $query = ViewCapMessagesGrouped::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $this->raggruppamento = Yii::$app->request->get('raggruppamento');

        if (!$this->validate()) {
            throw new \Exception(json_encode($this->getErrors()), 1);
            
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'raggruppamento' => $this->raggruppamento,
            'expires_rome_timezone' => $this->expires_rome_timezone,
            'identifier'=> $this->identifier,
            'risorsa'=> $this->risorsa,
            'incident'=> $this->incident,
            'event_type'=> $this->event_type,
            'event_subtype'=> $this->event_subtype,
            'event'=> $this->event,
            'type'=> $this->type,
            'status'=> $this->status,
            'segnalatore'=> $this->segnalatore,
            'sender'=> $this->sender,
            'sender_name'=> $this->sender_name,
            'code_call' => $this->code_call,
            'code_int' => $this->code_int,
            'formatted_status' => $this->formatted_status,
            'profile' => $this->profile,
            'major_event' => $this->major_event
        ]);

        if(!empty($this->string_comune)) {
            $query->andFilterWhere( ['string_comune' => $this->string_comune ]);
        }

        if(!empty($this->string_provincia)) {
            $query->andFilterWhere([ 'string_provincia' => $this->string_provincia ]);
        }

        return $dataProvider;
    }

    

}
