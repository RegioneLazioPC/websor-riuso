<?php

namespace common\models\cap;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class CapResources extends \yii\db\ActiveRecord
{
    const PREFIX_SEM = 'WSOR';

    public static $avaible_autentications = ['nessuna', 'basic'];
    public static $selectable_feeds = ['rss', 'atom'];
    public static $avaible_profiles = [ 'standard', 'vvf'];

    public function getSemaphore() {

        $str = self::PREFIX_SEM;
        $n = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $n .= ord($str[$i]);
        }
        
        $full = $n . $this->id;
        return (integer) $full;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
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
        return 'cap_resources';
    }

    

    public function rules() {
        return [
            [[
                'identifier',
                'url_feed_rss',
                'url_feed_atom',
                'preferred_feed',
                'profile',
                'raggruppamento',
                'autenticazione',
                'username',
                'password',
            ], 'string'],
            [['identifier'],'unique'],
            ['preferred_feed', 'validatePreferredFeed' ],
            ['profile', 'validateprofile'],
            ['autenticazione', 'validateAutenticazione'],
            [['url_feed_rss','url_feed_atom'], 'string'],
            [['url_feed_rss','url_feed_atom'], 'unique'],
            [['expiry'], 'integer'],
            [ ['username'], 'validateUserPwd'],
            [['raggruppamento'], 'required'],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'identifier' => 'Identificatore',
            'preferred_feed' => 'Feed preferito',
            'profile' => 'Profilo'
        ];
    }

    public function beforeSave( $insert )
    {
        $this->raggruppamento = strtoupper($this->raggruppamento);

        return parent::beforeSave($insert);
    }

    
    public function validatePreferredFeed($attribute, $params){
        if(empty($this->$attribute)) $this->addError("Attributo obbligatorio");

        if (!in_array($this->$attribute, self::$selectable_feeds)) $this->addError($attribute, 'Valore '.$this->$attribute.' non previsto, validi: ' . implode(", ", self::$selectable_feeds));
    }

    public function validateprofile($attribute, $params){
        if(empty($this->$attribute)) $this->addError("Attributo obbligatorio");

        if (!in_array($this->$attribute, self::$avaible_profiles)) $this->addError($attribute, 'Valore '.$this->$attribute.' non previsto, validi: ' . implode(", ", self::$avaible_profiles));
    }

    public function validateAutenticazione($attribute, $params){
        if(empty($this->$attribute)) $this->addError("Attributo obbligatorio");

        if (!in_array($this->$attribute, self::$avaible_autentications)) $this->addError($attribute, 'Valore '.$this->$attribute.' non previsto, validi: ' . implode(", ", self::$avaible_autentications));
    }

    public function validateUserPwd($attribute, $params){

        if($this->autenticazione != 'nessuna' && empty($this->$attribute)) $this->addError("Attributo obbligatorio");

    }



    public function search($params)
    {
        $query = CapResources::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }

}
