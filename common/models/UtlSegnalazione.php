<?php

namespace common\models;

use common\models\cap\CapExposedMessage;
use Yii;
use nanson\postgis\behaviors\GeometryBehavior;

use common\models\VolOrganizzazione;
use sammaye\audittrail\AuditTrail;
use common\models\cap\CapMessages;
use common\models\cap\CapMappingProfileEventTypes;

use common\models\cartografia\ViewCartografiaSegnalazione;
use common\models\LocComune;

/**
 * This is the model class for table "utl_segnalazione".
 *
 * @property integer $id
 * @property integer $idutente
 * @property string $foto
 * @property boolean $foto_locale
 * @property integer $tipologia_evento
 * @property integer $idsalaoperativa
 * @property string $note
 * @property double $lat
 * @property double $lon
 * @property string $direzione
 * @property integer $distanza
 * @property boolean $pericolo
 * @property boolean $feriti
 * @property boolean $interruzione_viabilita
 * @property boolean $aiuto_segnalatore
 * @property string $num_protocollo
 * @property string $cap_message_identifier
 * @property boolean $sos
 *
 * @property UtlUtente $utente
 * @property UtlExtraSegnalazione $extras
 */
class UtlSegnalazione extends \yii\db\ActiveRecord
{
    const FEED_CAP_FONTE = 'Feed CAP';
    public $address, $civico, $cap, $add_type, $google_address, $manual_address, $toponimo_address;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_segnalazione';
    }

    public function extraFields()
    {
        return [
            'tipologia',
            'utente'
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POINT,
                'attribute' => 'geom',
            ],
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['geom']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_type', 'id_indirizzo', 'id_civico', 'add_type', 'id_cap_message'], 'integer'],
            [['tipologia_evento'], 'required'], // 'lat', 'lon',
            ['num_protocollo', 'unique'],
            [['idutente', 'tipologia_evento', 'sottotipologia_evento', 'idcomune', 'idsalaoperativa', 'idcomune', 'id_organizzazione'], 'integer'],
            [['note'], 'string'],
            [['address', 'civico', 'cap', 'google_address', 'manual_address', 'toponimo_address', 'cap_message_identifier'], 'string'],
            [['lat', 'lon'], 'double'],
            [['pericolo', 'feriti', 'vittime', 'interruzione_viabilita', 'aiuto_segnalatore', 'foto_locale'], 'boolean'],
            [['direzione', 'foto', 'indirizzo', 'luogo', 'num_protocollo'], 'string', 'max' => 255],
            [['distanza'], 'string', 'max' => 100],
            [['id_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['id_organizzazione' => 'id']],
            // [['id_cap_message'], 'exist', 'skipOnError' => true, 'targetClass' => CapExposedMessage::className(), 'targetAttribute' => ['id_cap_message' => 'id']],
            [['idutente'], 'exist', 'skipOnError' => true, 'targetClass' => UtlUtente::className(), 'targetAttribute' => ['idutente' => 'id']],
            [['dataora_segnalazione', 'stato', 'fonte'], 'safe'],
            [['nome_segnalatore', 'cognome_segnalatore', 'email_segnalatore', 'telefono_segnalatore'], 'string']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['sos'] = ['lat', 'lon', 'fonte', 'stato', 'idcomune', 'indirizzo', 'idutente', 'sos', 'nome_segnalatore', 'cognome_segnalatore', 'email_segnalatore', 'telefono_segnalatore'];
        $scenarios['close'] = ['stato'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idutente' => 'Idutente',
            'foto' => 'Foto',
            'foto_locale' => 'Foto Locale',
            'tipologia_evento' => 'Tipologia Evento',
            'sottotipologia_evento' => 'Sottotipologia Evento',
            'note' => 'Note',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'direzione' => 'Direzione',
            'distanza' => 'Distanza',
            'pericolo' => 'Pericolo',
            'feriti' => 'Feriti',
            'vittime' => 'Vittime',
            'interruzione_viabilita' => 'Interruzione Viabilita',
            'aiuto_segnalatore' => 'Aiuto Segnalatore',
            'dataora_segnalazione' => 'Dataora segnalazione',
            'stato' => 'Stato',
            'fonte' => 'Fonte',
            'idcomune' => 'Comune *',
            'indirizzo' => 'Indirizzo/Località *',
            'luogo' => 'Luogo orientativo in assenza di comune, indirizzo/località',
            'num_protocollo' => 'N. Protocollo',
            'id_organizzazione' => 'Organizzazione',
            'cap_message_identifier' => 'Identificativo Cap Message'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtente()
    {
        return $this->hasOne(UtlUtente::className(), ['id' => 'idutente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipologia()
    {
        return $this->hasOne(UtlTipologia::className(), ['id' => 'tipologia_evento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSottotipologia()
    {
        return $this->hasOne(UtlTipologia::className(), ['id' => 'sottotipologia_evento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento'])
            ->viaTable('con_evento_segnalazione', ['idsegnalazione' => 'id']);
    }

    public function getLocIndirizzo()
    {
        return $this->hasOne(LocIndirizzo::className(), ['id' => 'id_indirizzo']);
    }

    public function getLocCivico()
    {
        return $this->hasOne(LocCivico::className(), ['id' => 'id_civico']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConSegnalazioneExtras()
    {
        return $this->hasMany(ConSegnalazioneExtra::className(), ['idsegnalazione' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtras()
    {

        return $this->hasMany(UtlExtraSegnalazione::className(), ['id' => 'idextra'])
            ->viaTable('con_segnalazione_extra', ['idsegnalazione' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachment()
    {
        return $this->hasOne(UtlSegnalazioneAttachments::className(), ['idsegnalazione' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'idcomune']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getLastSegnalazione()
    {

        return self::find()->where(['stato' => 'Nuova in lavorazione'])->orderBy('dataora_segnalazione DESC')->all();
    }

    /**
     * Restituisce l'array degli stati
     * @param in array $exclude (default:[]) Se inizializzato deve contenere la chiave o l'array di chiavi degli
     * stati da escludere
     * @return array
     */
    public static function getStatoArray($exclude = [])
    {
        if (!is_array($exclude)) $exclude = [$exclude => $exclude];
        $exclude = array_map(function ($key) {
            return [$key => $key];
        }, $exclude);
        $stati = array_diff_key([
            'Nuova in lavorazione' => 'Nuova in lavorazione',
            'Verificata e trasformata in evento' => 'Verificata e trasformata in evento',
            'Chiusa' => 'Chiusa'
        ], $exclude);


        return $stati;
    }

    public static function getFonteArray()
    {

        return [
            'Telefono' => 'Telefono',
            'Radio' => 'Radio',
            'Email' => 'Email',
            'App' => 'App'/*,
            self::FEED_CAP_FONTE => self::FEED_CAP_FONTE*/
        ];
    }

    public function getCapMessage()
    {
        return $this->hasOne(CapMessages::className(), ['id' => 'id_cap_message']);
    }

    public function getCapMessageByIdentifier()
    {
        return $this->hasOne(CapMessages::className(), ['identifier' => 'cap_message_identifier']);
    }

    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'id_organizzazione']);
    }

    public function getViewCartografia()
    {
        return $this->hasOne(ViewCartografiaSegnalazione::className(), ['id' => 'id']);
    }

    public function getMedia()
    {
        return $this->hasMany(UplMedia::className(), ['id' => 'id_media'])
            ->viaTable('con_upl_media_utl_segnalazione', ['id_segnalazione' => 'id']);
    }

    public function getAppEvento()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'id_evento'])
            ->via('segnalazioneAppEvento');
    }

    public function getSegnalazioneAppEvento()
    {
        return $this->hasOne(ConSegnalazioneAppEvento::className(), ['id_segnalazione' => 'id']);
    }

    public function getOperatori()
    {
        $connection = Yii::$app->getDb();
        $op_command = $connection->createCommand(
            '
            SELECT 
            concat("utl_anagrafica"."nome", \' \', "utl_anagrafica"."cognome") as anagrafica
            FROM "tbl_audit_trail"
            LEFT JOIN "user" ON "user"."id" = cast("tbl_audit_trail"."user_id" AS int) AND "tbl_audit_trail"."user_id" != \'\'
            LEFT JOIN "utl_operatore_pc" ON "utl_operatore_pc"."iduser" = "user"."id"
            LEFT JOIN "utl_anagrafica" ON "utl_anagrafica"."id" = "utl_operatore_pc"."id_anagrafica"
            WHERE "tbl_audit_trail"."model_id"::int = :model AND 
            "tbl_audit_trail"."model" = \'common\models\UtlSegnalazione\' AND 
            "utl_operatore_pc"."id" IS NOT NULL    
            GROUP BY "tbl_audit_trail"."user_id", "utl_anagrafica"."nome", "utl_anagrafica"."cognome", "utl_operatore_pc"."id"
            ',
            [':model' => $this->id]
        );

        $operatori = $op_command->queryAll();


        $return_array = [];
        foreach ($operatori as $user) {
            $return_array[] = $user['anagrafica'];
        }


        return array_unique($return_array);
    }


    /**
     * Inserisci coordinate
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert)
    {

        if ($this->lat && $this->lon) $this->geom = [$this->lon, $this->lat];

        // Cap message relation
        if (!empty($this->cap_message_identifier) && empty($this->id_cap_message)) {
            $capMessage = CapMessages::findOne(['identifier' => $this->cap_message_identifier]);
            if (!empty($capMessage)) {
                $this->id_cap_message = $capMessage->id;
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * Num. protocollo
     * @param  [type] $insert            [description]
     * @param  [type] $changedAttributes [description]
     * @return [type]                    [description]
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) :
            $this->num_protocollo = $this->id . "/" . date("Y");
            $this->save(false);
        endif;
    }

    /**
     * Da utilizzare in inserimento id_cap_message non lo facciamo editare
     * quando viene aperta la pagina del create se presente from_cap_message carichiamo i dati
     * 
     * @param  [type] $id_cap_message [description]
     * @return [type]                 [description]
     */
    public function loadDataFromCapMessage($id_cap_message)
    {
        $cap_message = CapMessages::findOne($id_cap_message);
        if (!$cap_message) return;


        $obj = \common\utils\cap\item\Base::getBaseItemFromProfile($cap_message->resource->profile, $cap_message->xml_content);
        if (!$obj) return;


        $this->address_type = 3;
        $this->id_cap_message = $id_cap_message;
        //$this->fonte = self::FEED_CAP_FONTE;
        $this->lat = $cap_message->lat;
        $this->lon = $cap_message->lon;
        $this->indirizzo = $obj->info[$cap_message->info_n]['address'];
        $this->nome_segnalatore = $cap_message->segnalatore;
        $this->cap_message_identifier = $cap_message->identifier;

        $tipo_evento = $obj->getTipoEvento($cap_message->info_n);
        $sottotipo_evento = $obj->getSottoTipoEvento($cap_message->info_n);
        if (!empty($tipo_evento)) {
            $map = CapMappingProfileEventTypes::findOne(['stringa_tipo_evento' => $tipo_evento]);
            if ($map) {
                $this->tipologia_evento = $map->id_tipo_evento;
                $this->sottotipologia_evento = $map->id_sottotipo_evento;
            }
        }

        if (!empty($sottotipo_evento)) {
            $map = CapMappingProfileEventTypes::findOne(['stringa_tipo_evento' => $sottotipo_evento]);
            if ($map) {
                $this->sottotipologia_evento = $map->id_sottotipo_evento;

                if (empty($this->tipologia_evento)) $this->tipologia_evento = $map->id_tipo_evento;
            }
        }

        if (!empty($this->lat) && !empty($this->lon)) {
            $command = Yii::$app->db->createCommand("SELECT pro_com
                FROM
                loc_comune_geom
                WHERE 
                 ST_DWithin(geom, ST_Transform(ST_SetSRID(ST_Point(:lon, :lat),4326), 32632 ), 3)
                 LIMIT 1", [':lon' => $this->lon, ':lat' => $this->lat]);

            $result = $command->queryAll();
            if (count($result) > 0) {
                $comune = LocComune::findOne(['codistat' => $result[0]['pro_com']]);

                if ($comune) $this->idcomune = $comune->id;
            } else {
                die();
            }
        }
    }
}
