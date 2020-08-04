<?php

namespace common\models;

use Yii;


class EvtSottostatoEvento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evt_sottostato_evento';
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

    public function getEventi() {
        return $this->hasMany( UtlEvento::className(), ['id_sottostato_evento'=>'id']);
    }

    public function getTipoEvento() {
        return $this->hasMany( UtlTipologia::className(), ['id'=>'id_tipo_evento'])
        ->viaTable('con_evt_sottostato_evento_utl_evento', ['id_sottostato_evento' => 'id']);
    }
}
