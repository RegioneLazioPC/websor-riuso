<?php

namespace common\models\organizzazione;

use Yii;

use common\models\utility\UtlContatto;
use common\models\VolSede;
/**
 * 
 *
 * @property int $id
 * @property int $id_sede
 * @property int $id_contatto
 *
 * @property UtlContatto $contatto
 * @property VolSede $organizzazione
 */
class ConSedeContatto extends \yii\db\ActiveRecord
{

    const TIPO_MESSAGGISTICA = 0;
    const TIPO_INGAGGIO = 1;
    const TIPO_ALLERTA = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'con_sede_contatto';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sede', 'id_contatto'], 'default', 'value' => null],
            [['id_sede', 'id_contatto', 'use_type', 'type'], 'integer'],
            [['note'], 'string'],
            [['id_contatto'], 'exist', 'skipOnError' => true, 'targetClass' => UtlContatto::className(), 'targetAttribute' => ['id_contatto' => 'id']],
            [['id_sede'], 'exist', 'skipOnError' => true, 'targetClass' => VolSede::className(), 'targetAttribute' => ['id_sede' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sede' => 'Sede',
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
    public function getSede()
    {
        return $this->hasOne(VolSede::className(), ['id' => 'id_sede']);
    }
}
