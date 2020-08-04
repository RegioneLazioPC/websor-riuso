<?php

namespace common\models;

use Yii;


class ConSchedaCocDocumenti extends \yii\db\ActiveRecord
{
    public $attachment;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_scheda_coc_documenti';
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
            [['id_upl_media', 'id_scheda_coc'], 'integer'],
            [['note'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_upl_media' => 'Media',
            'id_scheda_coc' => 'Scheda coc',
            'note' => 'Note'
        ];
    }

    public function getSchedaCoc()
    {
        return $this->hasOne( EvtSchedaCoc::className(), ['id'=>'id_scheda_coc']);
    }

    public function getUplMedia()
    {
        return $this->hasOne( UplMedia::className(), ['id'=> 'id_upl_media']);
    }
}
