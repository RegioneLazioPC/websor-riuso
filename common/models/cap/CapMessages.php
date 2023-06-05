<?php

namespace common\models\cap;

use Yii;
use common\models\cap\CapResources;
use common\models\cap\ConCapMessageIncident;
use common\models\cap\ConCapMessageReference;

use common\models\LocComune;

class CapMessages extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            //[
                //'class' => 'sammaye\audittrail\LoggableBehavior'
            //]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cap_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
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

    public function getResource()
    {
        return $this->hasOne(CapResources::className(), ['id'=>'id_resource']);
    }

    public function getIncidents()
    {
        return $this->hasMany(ConCapMessageIncident::className(), ['id_cap_message'=>'id']);
    }


    public function getReferences()
    {
        return $this->hasMany(ConCapMessageReference::className(), ['id_cap_message'=>'id']);
    }


    public static function buildFromResource(\common\utils\cap\item\Base $item, \common\models\cap\CapResources $risorsa)
    {
        
        $trans = Yii::$app->db->beginTransaction();

        try {
            $n = 0;
            foreach ($item->cap_formatted_item->info as $info_element) {
                $exist = CapMessages::findOne(['url'=>$item->url]);
                if ($exist) {
                    continue;
                }

                $message = new self;
                $message->cap_feed_url = $risorsa->preferred_feed == 'rss' ? $risorsa->url_feed_rss : $risorsa->url_feed_atom;
                $message->url = (string) $item->url;
                $message->identifier = $item->cap_formatted_item->identifier;
                $message->type = $item->cap_formatted_item->msgType;
                
                $message->xml_content = $item->plain_content;
                $message->json_content = $item->cap_formatted_item->array_data;
                
                $message->date_creation = (new \DateTime() )->format('Y-m-d H:i:s');
                $message->id_resource = $risorsa->id;
                $message->scheda = $item->cap_formatted_item->getScheda();
                $message->scheda_update = $item->cap_formatted_item->getSchedaUpdate();
                $message->status = $item->cap_formatted_item->status;
                $message->sender = $item->cap_formatted_item->sender;
                $message->sender_name = empty((array) $info_element['senderName']) ? '' : $info_element['senderName'];

                $cats = [];
                foreach ((array) $info_element['categories'] as $element) {
                    if (is_string($element)) {
                        $cats[] = $element;
                    }
                }

                $message->category = implode(", ", (array) $cats);
                $message->description = is_string(@$info_element['description']) ? @$info_element['description'] : '';
                $message->event = is_string(@$info_element['event']) ? @$info_element['event'] : '';
                
                $message->event_type = $item->cap_formatted_item->getTipoEvento($n);
                $message->event_subtype = $item->cap_formatted_item->getSottoTipoEvento($n);
                
                $message->segnalatore = $item->cap_formatted_item->source;

                $message->sent = $item->cap_formatted_item->sent->format('Y-m-d H:i:sP');
                $message->sent_rome_timezone = $item->cap_formatted_item->sent->format('Y-m-d H:i:s');
                
                $message->poly_geom = @$info_element['geometry'];
                $message->lat = @$info_element['lat'];
                $message->lon = @$info_element['lon'];
                $message->center_geom = @$info_element['center_geometry'];
                $message->info_n = $n;

                $message->call_time = $item->cap_formatted_item->getCallTime($n);
                $message->intervent_time = $item->cap_formatted_item->getIntTime($n);
                $message->arrival_time = $item->cap_formatted_item->getArrivalTime($n);
                $message->expires = $item->cap_formatted_item->getExpiresTime($n);
                $message->close_time = $item->cap_formatted_item->getCloseTime($n);
                $message->major_event = $item->cap_formatted_item->getMajorEvent($n);
                $message->profile = $item->cap_formatted_item->getProfile();
                $message->code_int = $item->cap_formatted_item->getCodeInt($n);
                $message->code_call = $item->cap_formatted_item->getCodeCall($n);
                $message->formatted_status = $item->cap_formatted_item->getFormattedStatus($n);

                $comune = self::calculateComune($message->lat, $message->lon);
                if ($comune) {
                    $message->id_comune = $comune->id;
                    $message->string_comune = $comune->comune;
                    $message->id_provincia = $comune->id_provincia;
                    $message->string_provincia = $comune->provincia_sigla;
                }
                
                if (!$message->save()) {
                    throw new \Exception(json_encode($message->getErrors()), 1);
                }

                foreach ($item->cap_formatted_item->incidents as $i) {
                    $incident = new ConCapMessageIncident;
                    $incident->id_cap_message = $message->id;
                    $incident->incident = $i;
                    $incident->sent_rome_timezone = $item->cap_formatted_item->sent->format('Y-m-d H:i:s');
                    if (!$incident->save()) {
                        throw new \Exception(json_encode($incident->getErrors()), 1);
                    }
                }

                foreach ($item->cap_formatted_item->references as $r) {
                    $reference = new ConCapMessageReference;
                    $reference->id_cap_message = $message->id;
                    $reference->reference = $r;
                    if (!$reference->save()) {
                        throw new \Exception(json_encode($reference->getErrors()), 1);
                    }
                }
                
                $n++;

                Yii::error('Salvato messaggio in cap_messages con id: ' . $message->id, 'cap');
            }

            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            throw $e;
        }
    }

    public static function calculateComune($lat, $lon)
    {

        if (empty($lat) || empty($lon)) {
            return null;
        }

        $command = Yii::$app->db->createCommand("SELECT pro_com
            FROM
            loc_comune_geom
            WHERE ".
             "
             ST_DWithin(geom, ST_Transform(ST_SetSRID(ST_Point(:lon, :lat),4326), 32632 ), 3)
             LIMIT 1", [ ':lon' => $lon, ':lat' => $lat ]);

        $result = $command->queryAll();

        if (count($result) > 0) {
            $comune = LocComune::findOne(['codistat' => $result[0]['pro_com']]);
            return $comune;
        } else {
            return null;
        }
    }
}
