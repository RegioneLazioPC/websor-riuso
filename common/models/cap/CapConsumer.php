<?php

namespace common\models\cap;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class CapConsumer extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CHANGE_PASSWORD = 'change-password';

    public $identity;
    public $password;

    public function init() {
        parent::init();
    }

    public function setIdentity( CapConsumer $consumer) {
        $this->identity = $consumer;
    }

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
        return 'cap_consumer';
    }

    

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    

    public function rules() {
        return [
            [['address','username','password'], 'required'],
            [['address','username'], 'unique'],
            [['password', 'username'], 'noDuePunti'],
            [['address'],'email'],
            [['comuni'], 'safe'],
            [['enabled', 'sala_operativa'], 'integer'],
            [['enabled', 'sala_operativa'], 'default', 'value' => 0],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = [
            'address',
            'username',
            'password',
            'comuni',
            'enabled',
            'sala_operativa',
        ];
        $scenarios[self::SCENARIO_UPDATE] = [
            'address',
            'username',
            'comuni',
            'enabled',
            'sala_operativa',
        ]; 
        $scenarios[self::SCENARIO_CHANGE_PASSWORD] = [
            
        ]; 
        return $scenarios;
    }

    public function noDuePunti($attribute_name, $params) {
        if(preg_match("/\:/", $this->$attribute_name))  $this->addError($attribute_name, "Non inserire due punti nel campo");
    }

    public function attributeLabels() {
        return [
            'address' => 'Address',
            'username' => 'Username',
            'comuni' => 'Comuni'
        ];
    }

    public function beforeSave( $insert )
    {
        if(!empty($this->comuni)) {
            $cm = json_decode($this->comuni);
            if(is_array($cm)) {

                $list = implode(",",array_map(function($pro_com){ return intval($pro_com);}, $cm));
                // ST_Expand ( 5000 )
                $this->geom = new \yii\db\Expression("(SELECT ST_UNION(geom) FROM loc_comune_geom WHERE pro_com IN ($list))");

            }
        } else {
            $this->geom = null;
        }

        return parent::beforeSave($insert);
    }

    public function impostaPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    

}
