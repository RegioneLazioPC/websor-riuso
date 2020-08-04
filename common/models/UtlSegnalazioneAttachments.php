<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_segnalazione_attachments".
 *
 * @property integer $id
 * @property integer $idsegnalazione
 * @property string $filename
 * @property string $date
 *
 * @property UtlSegnalazione $idsegnalazione0
 */
class UtlSegnalazioneAttachments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_segnalazione_attachments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idsegnalazione'], 'required'],
            [['idsegnalazione'], 'integer'],
            [['date'], 'safe'],
            [['filename'], 'string', 'max' => 255],
            [['idsegnalazione'], 'exist', 'skipOnError' => true, 'targetClass' => UtlSegnalazione::className(), 'targetAttribute' => ['idsegnalazione' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idsegnalazione' => 'Idsegnalazione',
            'filename' => 'Filename',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegnalazione()
    {
        return $this->hasOne(UtlSegnalazione::className(), ['id' => 'idsegnalazione']);
    }
}
