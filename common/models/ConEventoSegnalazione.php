<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "con_evento_segnalazione".
 *
 * @property integer $idsegnalazione
 * @property integer $idevento
 *
 * @property UtlEvento $idevento0
 * @property UtlSegnalazione $idsegnalazione0
 */
class ConEventoSegnalazione extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_evento_segnalazione';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idsegnalazione', 'idevento'], 'integer'],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
            [['idsegnalazione'], 'exist', 'skipOnError' => true, 'targetClass' => UtlSegnalazione::className(), 'targetAttribute' => ['idsegnalazione' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idsegnalazione' => 'Idsegnalazione',
            'idevento' => 'Idevento',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @deprecated
     */
    public function getIdevento0()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @deprecated
     */
    public function getIdsegnalazione0()
    {
        return $this->hasOne(UtlSegnalazione::className(), ['id' => 'idsegnalazione']);
    }
}
