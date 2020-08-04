<?php

namespace common\models;

use Yii;
use yii\web\Session;
use common\models\UtlAnagrafica;
use common\models\User;

/**
 * This is the model class for table "utl_operatore_pc".
 *
 * @property integer $id
 * @property integer $idsalaoperativa
 * @property integer $iduser
 * @property string $nome
 * @property string $cognome
 * @property string $email
 * @property string $matricola
 * @property string $ruolo
 *
 * @property UtlSalaOperativa $salaoperativa
 * @property User $user
 */
class UtlOperatorePc extends \yii\db\ActiveRecord
{
    use \common\traits\Everbridgable;
    /**
     * Necessario a Everbridgable per avere un riferimento all'identificativo in rubrica
     * @return [type] [description]
     */
    protected function getEverbridgeIdentifier() {
        return 'operatore_pc_' . $this->id;
    }

    public $stato, $fullname, $nome, $cognome, $email, $matricola, $username, $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_operatore_pc';
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
            [['idsalaoperativa', 'iduser', 'id_anagrafica'], 'integer'],
            [['username', 'password' ], 'required','on' => ['createUtente','createOperatore']],
            [[/*'nome', 'cognome', 'email', 'matricola', */'ruolo'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass'=>User::className(), 'targetAttribute'=>'email'],
            [['stato'], 'safe'],
            [['idsalaoperativa'], 'exist', 'skipOnError' => true, 'targetClass' => UtlSalaOperativa::className(), 'targetAttribute' => ['idsalaoperativa' => 'id']],
            [['iduser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['iduser' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['createOperatore'] = 
        ['matricola', 'nome', 'cognome', 'email', 'idsalaoperativa','ruolo','username', 'password']; 
        $scenarios['updateOperatore'] =
        ['matricola', 'nome', 'cognome', 'email', 'idsalaoperativa','ruolo','username', 'password'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idsalaoperativa' => 'Sala Operativa',
            'iduser' => 'Iduser',
            'ruolo' => 'Ruolo',
            'stato' => 'Stato'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalaoperativa()
    {
        return $this->hasOne(UtlSalaOperativa::className(), ['id' => 'idsalaoperativa']);
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'iduser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessionOperatore()
    {

        return $this->hasOne(DbSession::className(), ['id_user' => 'iduser']);
    }
   
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->viaTable('con_operatore_pc_contatto', ['id_operatore_pc' => 'id']);
    }
}
