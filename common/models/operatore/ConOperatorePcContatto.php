<?php

namespace common\models\operatore;

use Yii;

use common\models\utility\UtlContatto;
use common\models\UtlOperatorePc;
/**
 * 
 * @property int $id
 * @property int $id_operatore_pc
 * @property int $id_contatto
 *
 * @property UtlContatto $contatto
 * @property VolOrganizzazione $organizzazione
 */
class ConOperatorePcContatto extends \yii\db\ActiveRecord
{

    const TIPO_MESSAGGISTICA = 0;
    const TIPO_INGAGGIO = 1;
    const TIPO_ALLERTA = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'con_operatore_pc_contatto';
    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operatore_pc', 'id_contatto'], 'default', 'value' => null],
            [['id_operatore_pc', 'id_contatto', 'use_type', 'type'], 'integer'],
            [['id_contatto'], 'exist', 'skipOnError' => true, 'targetClass' => UtlContatto::className(), 'targetAttribute' => ['id_contatto' => 'id']],
            [['id_operatore_pc'], 'exist', 'skipOnError' => true, 'targetClass' => UtlOperatorePc::className(), 'targetAttribute' => ['id_operatore_pc' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_operatore_pc' => 'Operatore',
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
    public function getOperatore()
    {
        return $this->hasOne(UtlOperatorePc::className(), ['id' => 'id_operatore_pc']);
    }
}
