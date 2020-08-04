<?php
namespace api\modules\v1\models;

use yii\base\Model;
use common\models\User;
use yii\web\HttpException;

/**
 * Signup form
 */
class AppSignup extends Model
{
    public $username, $email, $password, $codfiscale, $telefono, $tipo_organizzazione, $codice_attivazione;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
            ['tipo_organizzazione', 'integer'],
            ['tipo_organizzazione, codice_attivazione', 'safe'],

            ['telefono', 'required', 'message' => 'Telefono non può essere vuoto.'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Indirizzo email già esistente'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['codfiscale', 'required'],
            ['codfiscale', 'exist', 'targetClass' => '\common\models\UtlAnagrafica', 'message' => 'Utente non abilitato, codice fiscale non trovato sul sistema'],

            ['codice_attivazione', 'required', 'when' => function($model) {
                return $model->tipo_organizzazione != 1;
            }]
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = strtolower($this->email);
        $user->email = strtolower($this->email);
        $user->status = User::STATUS_DELETED;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
