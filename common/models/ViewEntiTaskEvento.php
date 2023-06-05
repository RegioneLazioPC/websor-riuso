<?php

namespace common\models;

use Yii;
use common\models\UtlEvento;
use common\models\UtlIngaggio;


class ViewEntiTaskEvento extends \yii\db\ActiveRecord
{
    public $address, $civico, $cap, $add_type, $archived, $google_address, $manual_address, $toponimo_address, $list_distance;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_enti_task_evento';
    }

    public function rules()
    {
        return [
            [['id_utl_task'], 'integer'],
            [['descrizione_utl_task'], 'string'],
            [['address_type', 'id_indirizzo', 'id_civico', 'id_gestore_evento', 'archived'], 'integer'],
            [['tipologia_evento'], 'required'], //'lat', 'lon',
            [['sottotipologia_evento'], 'required', 'on' => ['create', 'update']],
            [['tipologia_evento', 'sottotipologia_evento', 'idparent', 'idcomune'], 'integer'],
            [['is_public'], 'boolean'],
            [['note', 'stato', 'num_protocollo'], 'string'],
            [['address', 'civico', 'cap'], 'string'],
            [['lat', 'lon'], 'double'],
            [['pericolo', 'feriti', 'vittime', 'interruzione_viabilita', 'aiuto_segnalatore'], 'boolean'],
            [['direzione', 'indirizzo', 'luogo'], 'string', 'max' => 255],
            [['direzione'], 'string', 'max' => 255],
            [['has_coc'], 'integer'],
            [['id_sottostato_evento'], 'integer'],
            [['distanza'], 'string', 'max' => 100],
            [['tipologia_evento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlTipologia::className(), 'targetAttribute' => ['tipologia_evento' => 'id']],
            [['id_sottostato_evento'], 'exist', 'skipOnError' => true, 'targetClass' => EvtSottostatoEvento::className(), 'targetAttribute' => ['id_sottostato_evento' => 'id']],
            [['id_gestore_evento'], 'exist', 'skipOnError' => true, 'targetClass' => EvtGestoreEvento::className(), 'targetAttribute' => ['id_gestore_evento' => 'id']],
            [['idparent'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idparent' => 'id']],
            [['dataora_evento', 'dataora_modifica', 'closed_at'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipologia_evento' => 'Tipologia Evento *',
            'id_sottostato_evento' => 'Stato evento',
            'sottotipologia_evento' => 'Sotto Tipologia Evento',
            'note' => 'Note',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'has_coc' => 'Assegnazione COC',
            'indirizzo' => 'Indirizzo/Località *',
            'direzione' => 'Direzione',
            'distanza' => 'Distanza',
            'pericolo' => 'Pericolo',
            'feriti' => 'Feriti',
            'vittime' => 'Vittime',
            'interruzione_viabilita' => 'Interruzione Viabilita',
            'aiuto_segnalatore' => 'Aiuto Segnalatore',
            'dataora_evento' => 'Dataora creazione evento',
            'dataora_modifica' => 'Dataora ultima modifica',
            'closed_at' => 'Data chiusura',
            'stato' => 'Stato',
            'num_protocollo' => 'N.Protocollo',
            'idcomune' => 'Comune *',
            'luogo' => 'Luogo',
            'id_gestore_evento' => 'Gestore',
            'is_public' => 'Pubblica',
            'archived' => 'Archiviato'
        ];
    }


    /**
     * Crea filtro nestato
     * @return [type] [description]
     */
    public static function getNestedFilterTipologie()
    {
        $genitori = \common\models\UtlTipologia::find()->where('idparent is null')->orderBy(['tipologia' => SORT_ASC])->asArray()->all();
        $figli = \common\models\UtlTipologia::find()->where('idparent is not null')->orderBy(['tipologia' => SORT_ASC])->asArray()->all();
        $types = [];
        $parent_keys = [];
        foreach ($genitori as $genitore) {
            $parent_keys[$genitore['id']] = $genitore['tipologia'];
            $types[$genitore['tipologia']] = [];
            $types[$genitore['tipologia']][$genitore['id']] = $genitore['tipologia'];
        }

        foreach ($figli as $figlio) {
            try {
                $types[$parent_keys[$figlio['idparent']]][$figlio['id']] = $figlio['tipologia'];
            } catch (\Exception $e) {
                // tiplogia genitore rimossa senza rimuovere il figlio
                Yii::error($e->getMessage());
            }
        }


        return $types;
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtras()
    {
        return $this->hasMany(UtlExtraSegnalazione::className(), ['id' => 'idextra'])
            ->viaTable('con_evento_extra', ['idevento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFronti()
    {
        return $this->hasMany(UtlEvento::className(), ['idparent' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGenitore()
    {
        return $this->hasMany(UtlEvento::className(), ['id' => 'idparent']);
    }

    public function getSpecializzazione()
    {
        return $this->hasMany(TblSezioneSpecialistica::className(), ['id' => 'id_tbl_sezione_specialistica'])
            ->viaTable('con_tipo_evento_specializzazione', ['id_utl_tipologia' => 'tipologia_evento']);
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
    public function getComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'idcomune']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegnalazioni()
    {
        return $this->hasMany(ConEventoSegnalazione::className(), ['idevento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegnalazioniAll()
    {
        return $this->hasMany(UtlSegnalazione::className(), ['id' => 'idsegnalazione'])->via('segnalazioni');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(ConOperatoreTask::className(), ['id' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatori()
    {
        return $this->hasMany(UtlOperatorePc::className(), ['id' => 'idoperatore'])
            ->viaTable('con_operatore_evento_task', ['idevento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRichiesteMezziAerei()
    {
        return $this->hasMany(RichiestaMezzoAereo::className(), ['idevento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRichiesteElicotteroUndeleted()
    {
        return $this->hasMany(RichiestaElicottero::className(), ['idevento' => 'id'])
            ->andOnCondition(['deleted' => 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRichiesteElicottero()
    {
        return $this->hasMany(RichiestaElicottero::className(), ['idevento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRichiesteDos()
    {
        return $this->hasMany(RichiestaDos::className(), ['idevento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRichiesteCanadair()
    {
        return $this->hasMany(RichiestaCanadair::className(), ['idevento' => 'id']);
    }

    public function getLocIndirizzo()
    {
        return $this->hasOne(LocIndirizzo::className(), ['id' => 'id_indirizzo']);
    }

    public function getLocCivico()
    {
        return $this->hasOne(LocCivico::className(), ['id' => 'id_civico']);
    }

    public function getGestore()
    {
        return $this->hasOne(EvtGestoreEvento::className(), ['id' => 'id_gestore_evento']);
    }

    public function getSottostato()
    {
        return $this->hasOne(EvtSottostatoEvento::className(), ['id' => 'id_sottostato_evento']);
    }

    public function getSchedacoc()
    {
        return $this->hasOne(EvtSchedaCoc::className(), ['id_evento' => 'id']);
    }

    public function getIngaggi()
    {
        return $this->hasMany(UtlIngaggio::className(), ['idevento' => 'id']);
    }

    public function getViewCartografia()
    {
        return $this->hasOne(ViewCartografiaEvento::className(), ['id' => 'id']);
    }

    public function getCapMessages()
    {
        return $this->hasMany(CapMessages::className(), ['id' => 'id_cap_message'])
            ->via('segnalazioniAll');
    }

    public function getLastCapMessages()
    {
        return $this->hasMany(\common\models\cap\ViewCapMessagesGrouped::className(), ['incident' => 'incident'])
            ->via('capMessagesBase');
    }

    public function getCapMessagesBase()
    {
        return $this->hasMany(\common\models\cap\ViewCapMessages::className(), ['id' => 'id_cap_message'])
            ->via('segnalazioniAll');
    }

    public function getOriginalCapMessage()
    {
        return $this->hasMany(CapMessages::className(), ['identifier' => 'cap_message_identifier'])
            ->via('segnalazioniAll');
    }

    public function getMainExposedCapMessage()
    {
        return $this->hasOne(CapExposedMessage::class, ['id_evento' => 'id'])->onCondition(['message_progr' => 0]);
    }

    public function getCapMessagesFromReference()
    {
        // Recupero il messaggio CAP originale collegato all'evento
        $mainCap = $this->getMainExposedCapMessage()->one();
        if(!$mainCap) return [];
        $data_sent = \DateTime::createFromFormat('Y-m-d H:i:sP', $mainCap->sent);
        if (is_bool($data_sent)) return [];
        
        $identifier = '%' . $mainCap->sender . ',' . $mainCap->identifier . ',' . $data_sent->format('c') . '%';
        return CapMessages::find()->where(
            "(json_content->'references')::text ilike :identifier", 
            [':identifier' => $identifier]
        )->orderBy(['sent'=>SORT_DESC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleOperativeEsterne()
    {
        return $this->hasMany(SalaOperativaEsterna::class, ['id' => 'id_sala_op_esterna'])
            ->viaTable('con_evento_sala_esterna', ['id_evento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getStatoOptions()
    {
        return [
            'Non gestito' => 'Non gestito',
            'In gestione' => 'In gestione',
            'Chiuso' => 'Chiuso'
        ];
    }
}
