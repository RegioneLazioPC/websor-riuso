<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "con_operatore_evento_task".
 *
 * @property integer $idoperatore
 * @property integer $idevento
 * @property integer $idfunzione_supporto
 * @property integer $idtask
 * @property string $dataora
 * @property string $note
 * @property boolean $is_task
 *
 * @property UtlFunzioniSupporto $idfunzioneSupporto
 * @property UtlEvento $idevento0
 * @property User $idoperatore0
 * @property UtlTask $idtask0
 */
class ConOperatoreEvento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_operatore_evento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idoperatore', 'idevento'], 'integer'],
            [['dataora'], 'safe'],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
            [['idoperatore'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['idoperatore' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idoperatore' => 'Idoperatore',
            'idevento' => 'Idevento',
            'idfunzione_supporto' => 'Idfunzione Supporto',
            'idtask' => 'Idtask',
            'dataora' => 'Dataora',
            'note' => 'Note',
            'is_task' => 'Is Task',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFunzioneSupporto()
    {
        return $this->hasOne(UtlFunzioniSupporto::className(), ['id' => 'idfunzione_supporto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdevento0()
    {
        return $this->hasOne(UtlEvento::className(), ['id' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatore()
    {
        return $this->hasOne(UtlOperatorePc::className(), ['id' => 'idoperatore']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(UtlTask::className(), ['id' => 'idtask']);
    }
}
