<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sala_operativa_esterna".
 *
 * @property integer $id
 * @property string $nome
 * @property string $url_endpoint
 * @property string $api_username
 * @property string $api_password
 * @property string $api_auth_url
 *
 */
class SalaOperativaEsterna extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sala_operativa_esterna';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'url_endpoint'], 'required'],
            [['nome', 'url_endpoint', 'api_username', 'api_password', 'api_auth_url'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome sala operativa',
            'url_enpoint' => 'Url per creazione messaggi CAP',
            'api_auth_url' => 'Api Authentication Url',
            'api_username' => 'Api Username',
            'api_password' => 'Api Password'
        ];
    }
}
