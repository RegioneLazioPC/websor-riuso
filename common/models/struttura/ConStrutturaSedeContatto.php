<?php

namespace common\models\struttura;

use Yii;

use common\models\utility\UtlContatto;
/**
 * 
 *
 * @property int $id
 * @property int $id_organizzazione
 * @property int $id_contatto
 *
 * @property UtlContatto $contatto
 * @property OrgOrganizzazione $organizzazione
 */
class ConStrutturaSedeContatto extends \yii\db\ActiveRecord
{

    const TIPO_MESSAGGISTICA = 0;
    const TIPO_INGAGGIO = 1;
    const TIPO_ALLERTA = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'con_struttura_sede_contatto';
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
            [['id_sede'], 'exist', 'skipOnError' => true, 'targetClass' => StrStrutturaSede::className(), 'targetAttribute' => ['id_sede' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sede' => 'Struttura',
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
    public function getStrutturaSede()
    {
        return $this->hasOne(StrStrutturaSede::className(), ['id' => 'id_sede']);
    }
}
