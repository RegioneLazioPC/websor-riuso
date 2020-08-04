<?php
namespace common\models;

use common\models\User;

use Yii;
use yii\base\Model;

class UserChangePassword extends Model
{
	public $old_password;
	public $new_password;
	public $repeat_password;

	
	public function rules()
	{
	  return [
		[['old_password, new_password, repeat_password'], 'required'],
		[['old_password, new_password, repeat_password'], 'string'],
		[['old_password'], 'findPasswords'],
		[['repeat_password'], 'comparePWD'],
	  ];
    }

    public function attributes() {
        return [
        	'new_password', 'old_password', 'repeat_password'
        ];
    }
	
	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'new_password' => 'Nuova password',
            'old_password' => 'Vecchia password',
            'repeat_password' => 'Conferma password'
        ];
    }
	
	
	public function findPasswords($attribute, $params, $validator)
	{
		$user = User::findOne(Yii::$app->user->identity->id);

		if(!$user->validatePassword($this->$attribute))  
			$this->addError($attribute, 'Vecchia password errata');
		
	}

	public function comparePWD($attribute, $params, $validator)
	{
		
		$new = Yii::$app->request->post('UserChangePassword');
		
		if(!isset($new['new_password']) || $new['new_password'] != $this->$attribute)  
			$this->addError($attribute, 'Le password non coincidono');
		
	}
}