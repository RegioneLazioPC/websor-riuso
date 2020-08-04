<?php

namespace common\models;

use Yii;


class EvtSchedaCoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evt_scheda_coc';
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
            [['id_evento'], 'integer'],
            [['data_apertura', 'data_chiusura'], 'safe'],
            [['data_apertura'], 'required'],
            [['num_atto'], 'string', 'max' => 255],
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
            'id_evento' => 'Evento',
            'data_apertura' => 'Data apertura',
            'data_chiusura' => 'Data chiusura',
            'num_atto' => 'Numero atto',
            'note' => 'Note'
        ];
    }

    public function getEvento()
    {
        return $this->hasOne( UtlEvento::className(), ['id'=>'id_evento']);
    }

    public function getDocumenti()
    {
        return $this->hasMany( ConSchedaCocDocumenti::className(), ['id_scheda_coc'=> 'id']);
    }

    public function getMedia()
    {
        return $this->hasMany( UplMedia::className(), ['id'=> 'id_upl_media'])
        ->via('documenti');
    }
}
