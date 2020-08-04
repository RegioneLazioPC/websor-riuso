<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_funzioni_supporto".
 *
 * @property integer $id
 * @property string $descrizione
 * @property string $code
 *
 * @property ConOperatoreTask[] $conOperatoreTasks
 */
class UtlFunzioniSupporto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_funzioni_supporto';
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
            [['code'], 'string', 'max' => 50],
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
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConOperatoreTasks()
    {
        return $this->hasMany(ConOperatoreTask::className(), ['idfunzione_supporto' => 'id']);
    }
}
