<?php

namespace common\models;

use Yii;
use common\models\VolOrganizzazione;
use common\models\TblSezioneSpecialistica;


class ConOrganizzazioneSezioneSpecialistica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_organizzazione_sezione_specialistica';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_organizzazione', 'id_sezione_specialistica'], 'integer'],
            [['id_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['id_organizzazione' => 'id']],
            [['id_sezione_specialistica'], 'exist', 'skipOnError' => true, 'targetClass' => TblSezioneSpecialistica::className(), 'targetAttribute' => ['id_sezione_specialistica' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_organizzazione' => 'Organizzazione',
            'id_sezione_specialistica' => 'Sezione specialistica',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'id_organizzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSezioneSpecialistica()
    {
        return $this->hasOne(TblSezioneSpecialistica::className(), ['id' => 'id_sezione_specialistica']);
    }
}
