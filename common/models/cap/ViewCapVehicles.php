<?php

namespace common\models\cap;

use Yii;


class ViewCapVehicles extends \yii\db\ActiveRecord
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
        return 'view_cap_vehicles';
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

    public function search($params, $paginate = true)
    {
        $query = ViewCapVehicles::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) return $dataProvider;
        

        return $dataProvider;

    }

}
