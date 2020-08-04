<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_ruolo_segnalatore".
 *
 * @property integer $id
 * @property string $descrizione
 */
class UtlRuoloSegnalatore extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_ruolo_segnalatore';
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
            [['descrizione'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descrizione' => 'Descrizione',
        ];
    }
}
