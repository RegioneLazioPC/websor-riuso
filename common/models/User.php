<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $operatore
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function getStatusString($status) {
        switch ($status) {
            case self::STATUS_ACTIVE:
                return 'ATTIVO';
                break;
            default:
                return 'BLOCCATO';
                break;
        }
    }

    public $spid_login = false;
    public $idp = null;
    public $spid_login_session_id = null;
    public $level = null;
    public $id_organizzazione = null;
    public $rappresentante_legale = 0;
    public $id_utl_utente = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    public function fields()
    {
        return [
            'id', 'email', 'username'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email','username'], 'unique'],
            [['email'], 'email'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['access_token', 'ip_address', 'user_agent'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        
        $token = Yii::$app->jwt->getParser()->parse((string) $token); 

        // valida anche user agent e indirizzo ip
        $request = new yii\web\Request;
        $ip = $request->getUserIP();
        $agent = $request->getUserAgent();

        if ( $agent != $token->getClaim( 'agent' ) ) {
            Yii::error('Utente UID: '.$token->getClaim( 'uid' ).' con AGENT: ' . $agent . ' diverso da AGENT in JWT: ' . $token->getClaim('agent') , 'api');
            throw new \yii\web\HttpException( 401, "Non sei autorizzato" );
        }

        if ( $ip != $token->getClaim( 'ip' ) ) {
            Yii::error('Utente UID: '.$token->getClaim( 'uid' ).' con IP: ' . $ip . ' diverso da IP in JWT: ' . $token->getClaim('ip') , 'api');
            //throw new \yii\web\HttpException( 401, "Non sei autorizzato" );
        }

        $user = static::findOne(['id' => $token->getClaim( 'uid' )]);
        if(!$user) return false;

        if($token->hasClaim('id_utl_utente') && $token->getClaim('id_utl_utente')) {
            $user->id_utl_utente = $token->getClaim('id_utl_utente');
        }

        if($token->hasClaim('id_organizzazione') && $token->getClaim('id_organizzazione')) {
            $user->id_organizzazione = $token->getClaim('id_organizzazione');
            $user->rappresentante_legale = $token->getClaim('rappresentante_legale');
        }

        if($token->hasClaim('spid_login') && $token->getClaim('spid_login')) {
            $user->spid_login_session_id = $token->getClaim('spid_login_session_id');
            $user->idp = $token->getClaim('idp');
            $user->level = $token->getClaim('level');
            $user->spid_login = $token->getClaim('spid_login');
        }

        return $user;
    }

    
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            //'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            Yii::error('TOKEN VUOTO', 'api');
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        $expiry_time = $timestamp + $expire;
        if($expiry_time < time()) Yii::error('TOKEN SCADUTO ' . $expiry_time . ' VERIFICANDO ' . $timestamp . ' DA TOKEN ' . $token, 'api');

        return $expiry_time >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Removes password reset token
     */
    public function getUtente()
    {
        return $this->hasOne(UtlUtente::className(), ['iduser' => 'id']);
    }

    /**
     * Removes password reset token
     */
    public function getOperatore()
    {
        return $this->hasOne(UtlOperatorePc::className(), ['iduser' => 'id']);
    }

    public function getRole($role_name)
    {
        $roles = Yii::$app->authManager->getRolesByUser( $this->id );
        return isset($roles[$role_name]);
    }

    public static function findByRoleName($role)
    {
        return static::find()
            ->join('LEFT JOIN','auth_assignment','auth_assignment.user_id::integer = id')
            ->where(['auth_assignment.item_name' => $role->name]);
    }

    public static function findByRolesName($roles)
    {
        return static::find()
            ->join('LEFT JOIN','auth_assignment','auth_assignment.user_id::integer = "user".id')
            ->where(['auth_assignment.item_name' => $roles]);
    }

    /**
     * Verifica almeno uno dei permessi
     * @param  array $permissions [description]
     * @return [type]              [description]
     */
    public function multipleCan($permissions)
    {
        try {
            foreach ($permissions as $permission) {
                if ( Yii::$app->user->can($permission) ) return true;
            }
        } catch(\Exception $e) {
            return false;
        }

        return false;
    }
}
