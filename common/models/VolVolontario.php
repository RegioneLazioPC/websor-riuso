<?php

namespace common\models;

use Yii;
use common\models\UtlUtente;

/**
 * This is the model class for table "vol_volontario".
 *
 * @property int $id
 * @property int $id_anagrafica
 * @property string $ruolo
 * @property string $spec_principale
 * @property string $valido_dal
 * @property string $valido_al
 * @property bool $operativo
 * @property int $id_organizzazione
 * @property int $id_sede
 * @property int $id_user
 *
 * @property ConVolontarioIngaggio[] $conVolontarioIngaggios
 * @property User $user
 * @property UtlAnagrafica $anagrafica
 * @property VolOrganizzazione $organizzazione
 * @property VolSede $sede
 */
class VolVolontario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_volontario';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['datore_di_lavoro'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_anagrafica', 'id_organizzazione', 'id_sede', 'id_user'], 'default', 'value' => null],
            [['id_anagrafica', 'id_organizzazione', 'id_sede', 'id_user'], 'integer'],
            [['ruolo', 'id_sync'], 'string'],
            [['valido_dal', 'valido_al'], 'safe'],
            [['operativo'], 'boolean'],
            [['spec_principale'], 'string', 'max' => 1],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
            [['id_anagrafica'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAnagrafica::className(), 'targetAttribute' => ['id_anagrafica' => 'id']],
            [['id_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['id_organizzazione' => 'id']],
            [['id_sede'], 'exist', 'skipOnError' => true, 'targetClass' => VolSede::className(), 'targetAttribute' => ['id_sede' => 'id']],
            [['datore_di_lavoro'], 'validateDatoreDiLavoro'],
            [['id_organizzazione', 'id_anagrafica'], 'required'],
        ];
    }

    public function validateDatoreDiLavoro($attribute_name, $params){
        return;
        /*
        $keys = [   
                    'denominazione',
                    'from',
                    'to',
                    'cfpiva',
                    'email',
                    'pec',
                    'tel',
                    'fax',
                    'via',
                    'civico',
                    'cap',
                    'pr',
                    'comune',
                ];

        $required_keys = [   
                    'denominazione',
                    'cfpiva',
                    'via',
                    'civico',
                    'cap',
                    'pr',
                    'comune',
                ];

        if(!is_array($this->$attribute_name)) {
            $this->addError($attribute_name, "Formato non valido");
        }

        foreach ($this->$attribute_name as $key => $value) {
            if(!in_array($key, $keys)) {
                $this->addError($attribute_name, "Formato non valido");
            }
        }

        foreach ($required_keys as $key) {
            if(!array_key_exists($key, $this->$attribute_name )) {
                $this->addError($attribute_name, "Formato ".$key." non valido");
            }
        }
        */

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_anagrafica' => 'Anagrafica',
            'ruolo' => 'Ruolo',
            'spec_principale' => 'Spec Principale',
            'valido_dal' => 'Valido Dal',
            'valido_al' => 'Valido Al',
            'operativo' => 'Operativo',
            'id_organizzazione' => 'Organizzazione',
            'id_sede' => 'Sede',
            'id_user' => 'Utente',
        ];
    }

    public function extraFields() {
        return ['anagrafica'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConVolontarioIngaggios()
    {
        return $this->hasMany(ConVolontarioIngaggio::className(), ['id_volontario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnagrafica()
    {
        return $this->hasOne(UtlAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'id_organizzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSede()
    {
        return $this->hasOne(VolSede::className(), ['id' => 'id_sede']);
    }

    public function getConContatto()
    {
        return $this->hasMany(\common\models\ConVolontarioContatto::className(), ['id_volontario'=>'id']);
    }

    /**
     * Contatti del volontario
     * @return [type] [description]
     */
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->viaTable('con_volontario_contatto', ['id_volontario' => 'id']);
    }

    /**
     * Indirizzi volontario
     * @return [type] [description]
     */
    public function getIndirizzo() {
        return $this->hasMany(\common\models\utility\UtlIndirizzo::className(), ['id' => 'id_indirizzo'])
        ->viaTable('con_volontario_indirizzo', ['id_volontario' => 'id']);
    }

    /**
     * Indirizzi volontario
     * @return [type] [description]
     */
    public function getSpecializzazione() {
        return $this->hasMany(\common\models\UtlSpecializzazione::className(), ['id' => 'id_specializzazione'])
        ->viaTable('con_volontario_specializzazione', ['id_volontario' => 'id']);
    }

    
}
