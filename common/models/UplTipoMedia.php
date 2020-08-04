<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "upl_tipo_media".
 *
 * @property int $id
 * @property string $descrizione
 * @property int $created_at
 * @property int $updated_at
 *
 */
class UplTipoMedia extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'upl_tipo_media';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
            [['descrizione'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descrizione' => 'Descrizione',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    
}
