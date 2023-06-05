<?php

namespace common\models\cap;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

use common\models\UtlEvento;

class CapExposedMessage extends \yii\db\ActiveRecord
{
    public $password;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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
        return 'cap_exposed_message';
    }

    public static function getDropdownCategories()
    {
        return [
            'Geo' => 'Geo',
            'Met' => 'Met',
            'Safety' => 'Safety',
            'Security' => 'Security',
            'Rescue' => 'Rescue',
            'Fire' => 'Fire',
            'Health' => 'Health',
            'Env' => 'Env',
            'Transport' => 'Transport',
            'Infra' => 'Infra',
            'CBRNE' => 'CBRNE',
            'Other' => 'Other',
        ];
    }


    public static function generateFromEvent(UtlEvento $evento, $action)
    {

        //message_progr
        //identifier

        $data_evento = \DateTime::createFromFormat('Y-m-d H:i:s', $evento->dataora_evento);
        $now = new \DateTime;

        $message = new self;
        $message->action_fire = $action;
        $message->identifier = new \yii\db\Expression("
            (
                SELECT CONCAT( :data_ora::text, '.', " . $evento->id . "::text, '.', (SELECT 
                        COALESCE(count(id),0)
                        FROM cap_exposed_message WHERE id_evento = :id_evento
                    )
                )
            )", [':data_ora' => $data_evento->format('Ymd'), ':id_evento' => $evento->id]);
        $message->message_progr = new \yii\db\Expression("(SELECT 
                        count(id)
                        FROM cap_exposed_message WHERE id_evento = :id_evento
                    )", [':id_evento' => $evento->id]);
        $message->id_evento = $evento->id;
        $message->sender = Yii::$app->params['cap']['sender'];
        $message->senderName = Yii::$app->params['cap']['sender'];
        $message->sent = (new \DateTime())->format("c");
        $message->status = 'Actual';
        $message->msgType = new \yii\db\Expression("(SELECT 
            CASE 
                WHEN count(id) = 0 THEN 'Alert'
                ELSE 'Update'
            END
            FROM cap_exposed_message WHERE id_evento = :id_evento
        )", [':id_evento' => $evento->id]);
        $message->source = Yii::$app->params['cap']['source'];
        $message->scope = Yii::$app->params['cap']['scope'];
        $message->restriction = (Yii::$app->params['cap']['scope'] == 'Restricted') ? new \yii\db\Expression(
            '(
                SELECT STRING_AGG( address, \' \' ) FROM cap_consumer
                WHERE
                sala_operativa = 1 AND
                address is not null AND
                (ST_DWithin( geom, ST_Transform( ST_SetSRID( ST_MakePoint(:lon, :lat), 4326), 32632), 5000 ) OR geom is null)
            )',
            [':lon' => $evento->lon, ':lat' => $evento->lat]
        ) : null;
        $message->addresses = (Yii::$app->params['cap']['scope'] == 'Private') ? new \yii\db\Expression(
            '(
                SELECT STRING_AGG( address, \' \' ) FROM cap_consumer
                WHERE
                sala_operativa = 1 AND
                address is not null AND
                (ST_DWithin( geom, ST_Transform( ST_SetSRID( ST_MakePoint(:lon, :lat), 4326), 32632), 5000 ) OR geom is null)
            )',
            [':lon' => $evento->lon, ':lat' => $evento->lat]
        ) : null;
        $message->code = Yii::$app->params['cap']['code'];
        $message->note = $evento->note;
        $message->incidents = Yii::$app->params['cap']['sender'] . "," . $data_evento->format('Ymd') . '.' . $evento->id . '.0' . "," . $data_evento->format('c');

        //$fromCapReference = [];
        //foreach ($evento->getOriginalCapMessage()->all() as $originaleMsg) {
            //$data_sent = \DateTime::createFromFormat('Y-m-d H:i:sP', $originaleMsg->sent);
            //if (is_bool($data_sent)) continue;
            //$fromCapReference[] = $originaleMsg->sender . ',' . $originaleMsg->identifier . ',' . $data_sent->format('c');
        //}
//
        //$fromCapReference = implode(' ', $fromCapReference);

        // se l'evento è un fronte prendo tutti i figli
        $message->references = new \yii\db\Expression("
            (
                SELECT 
                    STRING_AGG( CONCAT(sender,',',identifier,',',  to_jsonb(date_trunc('seconds', sent))->>0 ) , ' ') 
                FROM 
                (
                    (SELECT sender, identifier, sent FROM cap_exposed_message WHERE id_evento = :id_evento ORDER BY message_progr DESC LIMIT 1)
                    UNION ALL 
                    (
                        SELECT sender, identifier, sent FROM cap_exposed_message WHERE id_evento in (
                            SELECT id FROM utl_evento WHERE idparent = :id_evento
                        ) AND message_progr = 0
                    )
                    UNION ALL
                    (
                        SELECT sender, identifier, sent FROM cap_exposed_message WHERE id_evento = (
                            SELECT idparent FROM utl_evento WHERE id = :id_evento
                        ) AND message_progr = 0
                    )
                    UNION ALL 
                    (
                        SELECT sender, identifier, sent FROM cap_messages cm
                        LEFT JOIN utl_segnalazione s ON s.cap_message_identifier = cm.identifier
                        LEFT JOIN con_evento_segnalazione ces ON ces.idsegnalazione = s.id 
                        LEFT JOIN utl_evento e ON e.id = ces.idevento
                        WHERE e.id = :id_evento
                    )
                ) full_joined
            )    ", [':id_evento' => $evento->id]);

        
        $message->category = new \yii\db\Expression("(
            SELECT cap_category FROM utl_tipologia WHERE id = :id_tipo_evento
        )", [':id_tipo_evento' => $evento->tipologia_evento]);
        $message->event = $evento->tipologia->tipologia;
        $message->response_type = 'None';
        $message->urgency = 'Unknown';
        $message->severity = 'Unknown';
        $message->certainty = 'Unknown';
        $message->audience = 'Unknown';

        $event_code = [
            [
                'valueName' => 'Code_L1',
                'value' => $evento->tipologia->tipologia
            ],
        ];

        if (!empty($evento->sottotipologia)) {
            $event_code[] = [
                'valueName' => 'Code_L2',
                'value' => $evento->sottotipologia->tipologia
            ];
        }

        $message->eventCode = json_encode($event_code);
        $message->effective = $now->format('c');
        $message->onset = $data_evento->format('c');
        $message->expires = null;
        $message->senderName = Yii::$app->params['cap']['senderName'];
        $message->headline = $evento->tipologia->tipologia;
        $message->description = (string) $evento->note;
        $message->instruction = null;
        $message->web = "http://maps.google.it/maps?f=q&amp;source=s_q&amp;q=@" . $evento->lat . "," . $evento->lon . "&amp;hl=it&amp;t=h&amp;ie=UTF8&amp;z=18&amp;iwloc=A";
        $message->contact = Yii::$app->params['cap']['contact'];

        $n_v = 0;
        $vehicles = [];
        foreach ($evento->ingaggi as $attivazione) {
            if (in_array($attivazione->stato, [1, 3]) && empty($attivazione->motivazione_rifiuto) && !empty($attivazione->automezzo)) {
                $created = \DateTime::createFromFormat('Y-m-d H:i:s', $attivazione->created_at);
                $chiusura = \DateTime::createFromFormat('Y-m-d H:i:s', $attivazione->closed_at);

                $deviazione = '';
                try {
                    $deviazione = !empty($attivazione->deviato) && !is_bool($chiusura) ? $chiusura->format('c') : '';
                } catch (\Exception $e) {
                }
                $automezzo_data = [
                    self::replaceSplits(($attivazione->automezzo->targa ?? $attivazione->automezzo->id_sync) ?? '-'),
                    self::replaceSplits($attivazione->automezzo->tipo->descrizione),
                    $created->format('c'),
                    '',
                    !is_bool($chiusura) ? $chiusura->format('c') : '',
                    $deviazione,
                    $attivazione->id
                ];
                $n_v++;

                $vehicles[] = implode(",", $automezzo_data);
            }
        }


        $parameters = [
            [
                'valueName' => 'CODEINT',
                'value' => $evento->id
            ],
            [
                'valueName' => 'TIMECALL',
                'value' => $data_evento->format('c')
            ],
            [
                'valueName' => 'INCIDENTPROGRESS',
                'value' => $evento->stato == 'Chiuso' ? 'CLOSED' : (($n_v > 0) ? 'DISPATCHED' : 'CALL')
            ],
            [
                'valueName' => 'MAJOREVENT',
                'value' => 'N', //empty($evento->idparent) ? 'Y' : 'N'
            ],
            [
                'valueName' => 'REFNUM',
                'value' => $evento->num_protocollo
            ]
        ];

        if (!empty($evento->dataora_gestione)) {
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $evento->dataora_gestione);
            if (!is_bool($dt)) {
                $parameters[] = [
                    'valueName' => 'TIMEINT',
                    'value' => $dt->format('c')
                ];
            }
        }
        if (!empty($evento->closed_at)) {
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $evento->closed_at);
            if (!is_bool($dt)) {
                $parameters[] = [
                    'valueName' => 'TIMECANC',
                    'value' => $dt->format('c')
                ];
            }
        }



        if (count($vehicles) > 0) {
            $parameters[] = [
                'valueName' => 'VEHICLES',
                'value' => implode(" ", $vehicles)
            ];
        }

        $message->parameter = json_encode($parameters);

        $resource = [];
        foreach ($evento->segnalazioniAll as $segnalazione) {
            foreach ($segnalazione->media as $file) {
                $path = Yii::getAlias('@backend/uploads/');
                $file_path = $path . $file->ext . '/' . $file->date_upload . '/' . $file->nome;
                if (file_exists($file_path)) {
                    $resource[] = [
                        'resourceDesc' => $file->type->descrizione,
                        'mimeType' => $file->mime_type,
                        'derefUri' => base64_encode(file_get_contents($file_path)),
                        'digest' => sha1_file($file_path)
                    ];
                }
            }
        }
        $message->resource = json_encode($resource);

        $area = [
            'areaDesc' => !empty($evento->indirizzo) ? $evento->indirizzo : $evento->luogo,
            'circle' => $evento->lat . "," . $evento->lon . " 0.01"
        ];
        $message->area = json_encode($area);
        $message->lat = $evento->lat;
        $message->lon = $evento->lon;

        
        
        if (!$message->save()) {
            throw new \Exception(json_encode($message->getErrors()), 1);
        }
    }

    /**
     * Tolgo spazi e virgole per gli split mettendoli tra virgolette
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public static function replaceSplits($string)
    {

        if (preg_match("/\\s/", $string) || preg_match("/\,/", $string)) {
            $string = "\"" . $string . "\"";
        }

        return $string;
    }
}
