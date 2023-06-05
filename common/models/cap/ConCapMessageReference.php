<?php

namespace common\models\cap;

use Yii;
use common\models\cap\CapMessages;

class ConCapMessageReference extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_cap_message_reference';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_cap_message'], 'integer'],
            [['reference'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }

    public function getMessage() {
        return $this->hasOne(CapMessages::className(), ['id' => 'id_cap_message']);
    }

    

}