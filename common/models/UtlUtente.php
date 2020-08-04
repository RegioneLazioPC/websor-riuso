<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use common\models\UtlAnagrafica;
use common\models\UtlRuoloSegnalatore;
use common\models\MasRubrica;
use common\models\VolOrganizzazione;
use common\models\VolVolontario;


/**
 * This is the model class for table "utl_utente".
 *
 * @property integer $id
 * @property integer $iduser
 * @property string $nome
 * @property string $cognome
 * @property string $codfiscale
 * @property string $data_nascita
 * @property string $luogo_nascita
 * @property string $comune_residenza
 * @property string $telefono
 * @property string $smscode
 * @property string $sms_status
 * @property string $device_token
 * @property string $device_vendor
 * @property string $email
 * @property integer $codice_convenzione
 * @property integer $codice_attivazione
 * @property string $tipo
 *
 * @property UtlSegnalazione[] $utlSegnalaziones
 */
class UtlUtente extends \yii\db\ActiveRecord
{
    public $username, $password, 
    $data_nascita, $luogo_nascita,
    $nome, $cognome, $email, $codfiscale, $comune_residenza;

    
    //Save date un mysql format
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    // update 1 attribute 'created' OR multiple attribute ['created','updated']
                    ActiveRecord::EVENT_BEFORE_INSERT => ['data_nascita'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'data_nascita',
                ],
                'value' => function ($event) {
                    return Yii::$app->formatter->asDate($this->data_nascita, 'php:Y-m-d');
                },
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
        return 'utl_utente';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iduser', 'id_tipo_ente_pubblico','id_ruolo_segnalatore', 'id_anagrafica', 'enabled'], 'integer'],
            [['username', 'password' ], 'required','on' => ['default','createUtente']],
            [['username'], 'required','on' => ['updateUtente']],
            [['data_nascita', 'tipo', 'codice_attivazione'], 'safe'],
            [['telefono'], 'safe', 'on' => ['createUtenteApp']],
            [['tipo'],'required', 'on' => 'createSegnalatore' ],
            [['nome', 'cognome', 'codfiscale', 'luogo_nascita', 'email', 'comune_residenza', 'device_token', 'device_vendor'], 'string', 'max' => 255],
            [['telefono','smscode'], 'string', 'max' => 20],
            [['sms_status'], 'string', 'max' => 5],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Email utente esistente.', 'targetAttribute' => 'email', 'on' => 'updateUtente', 'when' => function ($model, $attribute) {
                return $model->{$attribute} !== $model->getOldAttribute($attribute);
            }],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Email utente esistente.', 'targetAttribute' => 'email', 'on' => 'createUtente'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Username  esistente.', 'targetAttribute' => 'username', 'on' => ['createUtente']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['createSegnalatore'] = ['nome', 'email', 'cognome', 'telefono','id_tipo_ente_pubblico','id_ruolo_segnalatore', 'tipo']; 
        $scenarios['updateUtente'] = ['nome', 'email', 'cognome', 'telefono', 'luogo_nascita', 'data_nascita', 'codfiscale', 'comune_residenza','id_ruolo_segnalatore']; 
        $scenarios['createUtente'] = ['nome', 'email', 'cognome', 'telefono', 'luogo_nascita', 'data_nascita', 'codfiscale', 'comune_residenza','id_ruolo_segnalatore']; 
        $scenarios['batchCreate'] = ['telefono','device_vendor','device_token','id_anagrafica'];
        $scenarios['attivaDisattiva'] = ['enabled'];
        $scenarios['createUtenteApp'] = ['email', 'telefono', 'codfiscale', 'device_token', 'device_vendor', 'enabled']; 
        $scenarios['syncUpdate'] = ['enabled'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'iduser' => 'Iduser',
            'nome' => 'Nome',
            'cognome' => 'Cognome',
            'codfiscale' => 'Codfiscale',
            'data_nascita' => 'Data Nascita',
            'luogo_nascita' => 'Luogo Nascita',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'password' => 'Password',
            'smscode' => 'Smscode',
            'tipo' => 'Tipo utente'
        ];
    }

    public static function getTipi() {
        return [
            1 => 'Cittadino Privato', 
            2 => 'Ente Pubblico', 
            3 => 'Organizzazione di Volontariato',
            4 => 'Operatore PC Sala operativa'
        ];
    }

    public function getTipo() {
        switch($this->tipo) {
            case 1:
            return 'Cittadino Privato';
            break;
            case 2:
            return 'Ente Pubblico';
            break;
            case 3:
            return 'Organizzazione di Volontariato';
            break;
            case 4:
            return 'Operatore PC Sala operativa';
            break;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlSegnalaziones()
    {
        return $this->hasMany(UtlSegnalazione::className(), ['idutente' => 'id']);
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
        return $this->hasMany(VolOrganizzazione::className(), ['id' => 'id_organizzazione'])
        ->via('volontarioOrg');
    }

    public function getVolontarioOrg()
    {
        return $this->hasMany(VolVolontario::className(), ['id_anagrafica' => 'id_anagrafica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtras()
    {
        return $this->hasOne(UtlExtraUtente::className(), ['id' => 'id_tipo_ente_pubblico']);
        
    }

    public function getRubrica() {
        return $this->hasOne(MasRubrica::className(), ['id_anagrafica'=>'id_anagrafica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'iduser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuoloSegnalatore()
    {
        return $this->hasOne(UtlRuoloSegnalatore::className(), ['id' => 'id_ruolo_segnalatore']);
    }

    public function beforeSave($insert)
    {
        if(!$this->data_nascita) :
            $now = new \DateTime();
            $this->data_nascita = $now->format('Y-m-d');
        endif;
        return parent::beforeSave($insert);
    }

    public function getVolontario()
    {
        return $this->hasOne(VolVolontario::className(), ['id_anagrafica'=>'id_anagrafica'])->where(['operativo'=>TRUE]);
    }
}
