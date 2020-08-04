<?php

namespace common\models;

use Yii;

use common\models\utility\UtlContatto;
use common\models\organizzazione\ConOrganizzazioneContatto;
/**
 * This is the model class for table "vol_organizzazione".
 *
 * @property integer $id
 * @property integer $id_tipo_organizzazione
 * @property string $denominazione
 * @property string $codicefiscale
 * @property string $partita_iva
 * @property string $tipo_albo_regionale
 * @property integer $num_albo_regionale
 * @property string $data_albo_regionale
 * @property integer $num_albo_provinciale
 * @property integer $num_albo_nazionale
 * @property integer $num_assicurazione
 * @property string $societa_assicurazione
 * @property string $data_scadenza_assicurazione
 * @property string $note
 *
 * @property VolTipoOrganizzazione $idTipoOrganizzazione
 * @property VolSede[] $volSedes
 */
class VolOrganizzazione extends \yii\db\ActiveRecord
{
    use \common\traits\Everbridgable;

    const SCENARIO_UPDATE_SYNCED = 'update_synced'; // permette la modifica solo di note e num_comunale

    public $provincia, $comune, $sezione_specialistica, 
    $zone_allerta_array, $manual_zona_update;

    const STATO_ATTIVA = 3;
    /**
     * Necessario a Everbridgable per avere un riferimento all'identificativo in rubrica
     * @return [type] [description]
     */
    protected function getEverbridgeIdentifier() {
        return 'organizzazione_' . $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_organizzazione';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stato_iscrizione'], 'default', 'value' => 3],
            [['id_tipo_organizzazione', 'num_albo_provinciale', 'num_albo_nazionale', 'num_assicurazione', 'ref_id', 'num_comunale'], 'integer'],
            [['denominazione', 'tipo_albo_regionale', 'societa_assicurazione', 'note', 'num_albo_regionale',
                'email_responsabile', 'pec_responsabile', 'nome_responsabile', 'tel_responsabile',
                'tel_referente', 'email_referente', 'fax_referente', 'nome_referente','id_sync',
                'cf_rappresentante_legale','cf_referente'
            ], 'string'],
            [['codicefiscale', 'partita_iva'], 'string', 'max' => 16 ],
            [['data_albo_regionale', 'data_scadenza_assicurazione','data_costituzione'], 'safe'],
            [['id_tipo_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolTipoOrganizzazione::className(), 'targetAttribute' => ['id_tipo_organizzazione' => 'id']],
            [['id_tipo_organizzazione', 'codicefiscale', 'denominazione', 'ref_id'], 'required'],
            [['update_zona_allerta_strategy'], 'integer'],
            [['zone_allerta'],'string'],
            [['zone_allerta_array'], 'safe'],
            [['manual_zona_update'], 'integer'],
            [['ambito'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_tipo_organizzazione' => 'Tipo Organizzazione',
            'num_comunale' => 'Numero comunale',
            'denominazione' => 'Denominazione',
            'codicefiscale' => 'Codicefiscale',
            'partita_iva' => 'Partita Iva',
            'tipo_albo_regionale' => 'Tipo Albo Regionale',
            'num_albo_regionale' => 'Num Albo Regionale',
            'data_albo_regionale' => 'Data Albo Regionale',
            'num_albo_provinciale' => 'Num Albo Provinciale',
            'num_albo_nazionale' => 'Num Albo Nazionale',
            'num_assicurazione' => 'Num Assicurazione',
            'societa_assicurazione' => 'Societa Assicurazione',
            'data_scadenza_assicurazione' => 'Data Scadenza Assicurazione',
            'note' => 'Note',
            'data_costituzione' => 'Data costituzione',
            'ref_id' => 'Identificativo',
            'email_responsabile' => 'Email responsabile',
            'pec_responsabile' => 'Pec responsabile',
            'nome_responsabile' => 'Nome responsabile',
            'tel_responsabile' =>'Telefono responsabile',
            'tel_referente' =>'Telefono referente',
            'email_referente' =>'Email referente',
            'fax_referente' =>'Fax referente',
            'nome_referente' =>'Nome referente',
            'stato_iscrizione' => 'Iscrizione',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE_SYNCED] = ['note', 'num_comunale',
        'update_zona_allerta_strategy','zone_allerta'];
        
        return $scenarios;
    }

    public function getNomeStato() 
    {
        return ($this->stato_iscrizione == VolOrganizzazione::STATO_ATTIVA) ? 'Attiva' : 'Non attiva';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoOrganizzazione()
    {
        return $this->hasOne(VolTipoOrganizzazione::className(), ['id' => 'id_tipo_organizzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolSedes()
    {
        return $this->hasMany(VolSede::className(), ['id_organizzazione' => 'id']);
    }

    public function getAutomezzi()
    {
        return $this->hasMany(UtlAutomezzo::className(), ['idorganizzazione'=>'id']);
    }

    public function getAttrezzature()
    {
        return $this->hasMany(UtlAttrezzatura::className(), ['idorganizzazione'=>'id']);
    }

    /**
     * Contatti della sede
     * @return [type] [description]
     */
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->via('conContatto');
    }


    public function getConContatto()
    {
        return $this->hasMany(\common\models\organizzazione\ConOrganizzazioneContatto::className(), ['id_organizzazione'=>'id']);
    }

    public function getContattiAttivazioni() {
        return $this->hasMany( ConOrganizzazioneContatto::className(), ['id_organizzazione' => 'id'])->where(['use_type'=>1]);
    }

    public function getContattoAttivazioni() {
        return $this->hasMany( UtlContatto::className(), ['id' => 'id_contatto'])
        ->via('contattiAttivazioni');
    }

    
    public function getSezioneSpecialistica() {
        return $this->hasMany(\common\models\TblSezioneSpecialistica::className(), ['id' => 'id_sezione_specialistica'])
        ->viaTable('con_organizzazione_sezione_specialistica', ['id_organizzazione' => 'id']);
    }

    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert || isset($changedAttributes['update_zona_allerta_strategy']))
        {
            $this->updateZone();
        }
    }

    public function updateZone() {
        switch($this->update_zona_allerta_strategy) {
            case 0:
                $cmd = Yii::$app->db->createCommand("UPDATE vol_organizzazione 
                SET 
                zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG( distinct z.code ORDER BY z.code ASC), ',') 
                    FROM vol_sede sede
                    LEFT JOIN loc_comune c ON c.id = sede.comune
                    LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                    LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                    WHERE sede.id_organizzazione = :id_organizzazione )
                WHERE id = :id_organizzazione");
                $cmd->bindValues([
                    ':id_organizzazione' => $this->id
                ]);
            break;
            case 1:
                $cmd = Yii::$app->db->createCommand("UPDATE vol_organizzazione 
                SET 
                zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG( distinct z.code ORDER BY z.code ASC), ',') 
                    FROM vol_sede sede
                    LEFT JOIN loc_comune com ON com.id = sede.comune
                    LEFT JOIN loc_provincia p ON p.id = com.id_provincia
                    LEFT JOIN loc_comune c ON c.id_provincia = p.id
                    LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                    LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                    WHERE sede.id_organizzazione = :id_organizzazione )
                WHERE id = :id_organizzazione");
                $cmd->bindValues([
                    ':id_organizzazione' => $this->id
                ]);
            break;
            case 2:
                $cmd = Yii::$app->db->createCommand("UPDATE vol_organizzazione 
                SET 
                zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG( distinct alm_zona_allerta.code ORDER BY alm_zona_allerta.code ASC), ',') FROM alm_zona_allerta )
                WHERE id = :id_organizzazione");
                $cmd->bindValues([
                    ':id_organizzazione' => $this->id
                ]);
            break;
            case 3:
                // void
                return;
            break;
            default:
                // void
                return;
            break;
        }

        $cmd->execute();
    }





}
