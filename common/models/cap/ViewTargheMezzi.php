<?php

namespace common\models\cap;
use yii\data\ActiveDataProvider;
use common\models\UtlSegnalazione;

use Yii;


class ViewTargheMezzi extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_targhe_mezzi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }

}
