<?php

namespace common\models\ente;

use Yii;

use common\models\utility\UtlContatto;


class ConEnteContatto extends \yii\db\ActiveRecord
{

    const TIPO_MESSAGGISTICA = 0;
    const TIPO_INGAGGIO = 1;
    const TIPO_ALLERTA = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'con_ente_contatto';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_ente', 'id_contatto'], 'default', 'value' => null],
            [['id_ente', 'id_contatto', 'use_type', 'type'], 'integer'],
            [['note'], 'string'],
            [['id_contatto'], 'exist', 'skipOnError' => true, 'targetClass' => UtlContatto::className(), 'targetAttribute' => ['id_contatto' => 'id']],
            [['id_ente'], 'exist', 'skipOnError' => true, 'targetClass' => EntEnte::className(), 'targetAttribute' => ['id_ente' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ente' => 'Ente',
            'id_contatto' => 'Id Contatto',
            'type' => 'Tipo'
        ];
    }

    public function extraFields() {
        return ['contatto'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContatto()
    {
        return $this->hasOne(UtlContatto::className(), ['id' => 'id_contatto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnte()
    {
        return $this->hasOne(EntEnte::className(), ['id' => 'id_ente']);
    }
}
