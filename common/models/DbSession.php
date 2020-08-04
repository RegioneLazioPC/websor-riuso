<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property string $id
 * @property integer $expire
 * @property resource $data
 * @property integer $id_user
 * @property string $last_write
 */
class DbSession extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire', 'id_user'], 'integer'],
            [['data'], 'string'],
            [['last_write'], 'safe'],
            [['id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expire' => 'Expire',
            'data' => 'Data',
            'id_user' => 'Id User',
            'last_write' => 'Last Write',
        ];
    }
}
