<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "con_ispezione_soggetti".
 * @deprecated
 * 
 * @property integer $idispezione
 * @property integer $idsoggetto
 * @property string $created_at
 *
 * @property IspIspezione $idispezione0
 * @property IspSoggettiIspezione $idsoggetto0
 */
class ConIspezioneSoggetti extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_ispezione_soggetti';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idispezione', 'idsoggetto'], 'required'],
            [['idispezione', 'idsoggetto'], 'integer'],
            [['created_at'], 'safe'],
            [['idispezione'], 'exist', 'skipOnError' => true, 'targetClass' => IspIspezione::className(), 'targetAttribute' => ['idispezione' => 'id']],
            [['idsoggetto'], 'exist', 'skipOnError' => true, 'targetClass' => IspSoggettiIspezione::className(), 'targetAttribute' => ['idsoggetto' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idispezione' => 'Idispezione',
            'idsoggetto' => 'Idsoggetto',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdispezione0()
    {
        return $this->hasOne(IspIspezione::className(), ['id' => 'idispezione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdsoggetto0()
    {
        return $this->hasOne(IspSoggettiIspezione::className(), ['id' => 'idsoggetto']);
    }
}
