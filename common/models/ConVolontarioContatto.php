<?php

namespace common\models;

use Yii;

use common\models\utility\UtlContatto;
use common\models\VolVolontario;


class ConVolontarioContatto extends \yii\db\ActiveRecord
{

    const TIPO_MESSAGGISTICA = 0;
    const TIPO_INGAGGIO = 1;
    const TIPO_ALLERTA = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'con_volontario_contatto';
    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_volontario', 'id_contatto'], 'default', 'value' => null],
            [['id_volontario', 'id_contatto', 'use_type'], 'integer'],
            [['note'], 'string'],
            [['id_contatto'], 'exist', 'skipOnError' => true, 'targetClass' => UtlContatto::className(), 'targetAttribute' => ['id_contatto' => 'id']],
            [['id_volontario'], 'exist', 'skipOnError' => true, 'targetClass' => VolVolontario::className(), 'targetAttribute' => ['id_volontario' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_volontario' => 'Volontario',
            'id_contatto' => 'Id Contatto',
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
    public function getVolontario()
    {
        return $this->hasOne(VolVolontario::className(), ['id' => 'id_volontario']);
    }
}
