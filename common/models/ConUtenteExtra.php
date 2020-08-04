<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "con_utente_extra".
 *
 * @property integer $idutente
 * @property integer $idextra
 *
 * @property UtlUtente $idutente0
 * @property UtlExtraUtente $idextra0
 */
class ConUtenteExtra extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_utente_extra';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idutente', 'idextra'], 'integer'],
            [['idutente'], 'exist', 'skipOnError' => true, 'targetClass' => UtlUtente::className(), 'targetAttribute' => ['idutente' => 'id']],
            [['idextra'], 'exist', 'skipOnError' => true, 'targetClass' => UtlExtraUtente::className(), 'targetAttribute' => ['idextra' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idutente' => 'Idutente',
            'idextra' => 'Idextra',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdutente0()
    {
        return $this->hasOne(UtlUtente::className(), ['id' => 'idutente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdextra0()
    {
        return $this->hasOne(UtlExtraUtente::className(), ['id' => 'idextra']);
    }
}
