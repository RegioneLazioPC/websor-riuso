<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "alm_tipo_allerta".
 *
 * @property integer $id
 * @property string $tipologia
 *
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos0
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos1
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos2
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos3
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos4
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos5
 * @property AlmAllertaMeteo[] $AlmAllertaMeteos6
 */
class AlmTipoAllerta extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
        return 'alm_tipo_allerta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipologia'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipologia' => 'Tipologia',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala1' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos0()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala2' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos1()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala3' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos2()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala4' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos3()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala5' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos4()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala6' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos5()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala7' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmAllertaMeteos6()
    {
        return $this->hasMany(AlmAllertaMeteo::className(), ['cala8' => 'id']);
    }
}
