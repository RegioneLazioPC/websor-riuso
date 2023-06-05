<?php

namespace common\models;

use Yii;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vol_convenzione".
 *
 * @property integer $id
 * @property integer $id_organizzazione
 * @property integer $id_ref
 * @property string  $num_riferimento
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property VolOrganizzazione $organizzazione
 */
class VolConvenzione extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_convenzione';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_organizzazione', 'id_ref'], 'required'],
            [['id_organizzazione', 'id_ref', 'created_at', 'updated_at'], 'integer'],
            [['num_riferimento'], 'string'],
            [['id_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::class, 'targetAttribute' => ['id_organizzazione' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_organizzazione' => 'Organizzazione',
            'id_ref' => 'Id organizzazione MGO',
            'num_riferimento' => 'Numero riferimento',
            'created_at' => 'Creato il',
            'updated_at' => 'Modificato il',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::class, ['id' => 'id_organizzazione']);
    }
}
