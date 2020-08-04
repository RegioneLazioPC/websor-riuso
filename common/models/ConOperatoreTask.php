<?php

namespace common\models;

use Yii;
use backend\events\EditedUtlEventoEvent;

/**
 * This is the model class for table "con_operatore_evento_task".
 *
 * @property integer $idoperatore
 * @property integer $idevento
 * @property integer $idfunzione_supporto
 * @property integer $idtask
 * @property integer $idsquadra
 * @property string $dataora
 * @property string $note
 * @property boolean $is_task
 *
 * @property UtlFunzioniSupporto $idfunzioneSupporto
 * @property UtlEvento $idevento0
 * @property User $idoperatore0
 * @property UtlTask $idtask0
 * @property UtlAutomezzo $automezzo
 */
class ConOperatoreTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_operatore_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idoperatore', 'idevento', 'idfunzione_supporto', 'idtask', 'idsquadra', 'idautomezzo'], 'integer'],
            [['dataora'], 'safe'],
            [['is_task'], 'boolean'],
            [['note'], 'string', 'max' => 1000],
            [['idfunzione_supporto'], 'exist', 'skipOnError' => true, 'targetClass' => UtlFunzioniSupporto::className(), 'targetAttribute' => ['idfunzione_supporto' => 'id']],
            [['idevento'], 'exist', 'skipOnError' => true, 'targetClass' => UtlEvento::className(), 'targetAttribute' => ['idevento' => 'id']],
            [['idoperatore'], 'exist', 'skipOnError' => true, 'targetClass' => UtlOperatorePc::className(), 'targetAttribute' => ['idoperatore' => 'id']],
            [['idtask'], 'exist', 'skipOnError' => true, 'targetClass' => UtlTask::className(), 'targetAttribute' => ['idtask' => 'id']],
            [['idfunzione_supporto','idtask','note'], 'required', 'when' => function($model, $attribute) {
                Yii::error('model');
                return empty($model->idtask) && empty($model->note) && empty($model->idfunzione_supporto);
            }, 'message' => 'Inserisci almeno un campo']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idoperatore' => 'Idoperatore',
            'idevento' => 'Idevento',
            'idfunzione_supporto' => 'Idfunzione Supporto',
            'idtask' => 'Idtask',
            'idsquadra' => 'Idsquadra',
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
    public function getEvento()
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSquadra()
    {
        return $this->hasOne(UtlSquadraOperativa::className(), ['id' => 'idsquadra']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutomezzo()
    {
        return $this->hasOne(UtlAutomezzo::className(), ['id' => 'idautomezzo']);
    }

    public function afterSave($insert, $changedAttributes) 
    {
        parent::afterSave($insert, $changedAttributes);
        
        EditedUtlEventoEvent::handleEdited($this->idevento);
    }
}
