<?php

namespace common\models\organizzazione;

use Yii;

use common\models\utility\UtlContatto;
use common\models\VolOrganizzazione;
/**
 * 
 * @property int $id
 * @property int $id_organizzazione
 * @property int $id_contatto
 *
 * @property UtlContatto $contatto
 * @property VolOrganizzazione $organizzazione
 */
class ConOrganizzazioneContatto extends \yii\db\ActiveRecord
{

    const TIPO_MESSAGGISTICA = 0;
    const TIPO_INGAGGIO = 1;
    const TIPO_ALLERTA = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'con_organizzazione_contatto';
    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_organizzazione', 'id_contatto'], 'default', 'value' => null],
            [['id_organizzazione', 'id_contatto', 'use_type', 'type'], 'integer'],
            [['note'], 'string'],
            [['id_contatto'], 'exist', 'skipOnError' => true, 'targetClass' => UtlContatto::className(), 'targetAttribute' => ['id_contatto' => 'id']],
            [['id_organizzazione'], 'exist', 'skipOnError' => true, 'targetClass' => VolOrganizzazione::className(), 'targetAttribute' => ['id_organizzazione' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_organizzazione' => 'Organizzazione',
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
    public function getOrganizzazione()
    {
        return $this->hasOne(VolOrganizzazione::className(), ['id' => 'id_organizzazione']);
    }
}
