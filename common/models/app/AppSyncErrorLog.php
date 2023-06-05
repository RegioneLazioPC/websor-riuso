<?php

namespace common\models\app;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class AppSyncErrorLog extends \yii\db\ActiveRecord
{
    
    public function init() {
        parent::init();
    }


    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_sync_error_log';
    }
    

    public function rules() {
        return [
            [['service','stack', 'level'], 'required'],
            [['service','stack', 'level'], 'string'],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public static function createError($service, $error, $level = 'FATAL') {
        $model = new self;
        $model->service = $service;
        $model->stack = $error;
        $model->level = $level;
        if(!$model->save()){
            Yii::error( 
                "ERRORE SALVATAGGIO LOG ERRORE: " . json_encode($model->getErrors()), 'sync'
            );
        }
    }
    

}
