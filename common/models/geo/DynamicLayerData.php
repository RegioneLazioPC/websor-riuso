<?php

namespace common\models\geo;

use Yii;
use yii\data\ActiveDataProvider;


class DynamicLayerData extends \yii\db\ActiveRecord
{
    public static $table_name;
    public $rules;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return Yii::$app->params['geo_layer'] . "." . self::$table_name;
    }
    /*
    public static function getTableSchema() {
        $obj_schema = new \stdClass;
        $obj_schema->columns = 
    }*/

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->rules;
    }


    public function extraFields() {
        return [];
    }

    public function search($table, $query_params) {
        DynamicLayerData::$table_name = $table;

        $query = self::find();

        $this->load($query_params);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $prms = isset($query_params['DynamicLayerData']) ? $query_params['DynamicLayerData'] : [];
        foreach ($prms as $key => $value) {
            $query->andFilterWhere(['ilike', '('.$key.')::text', $value]);
        }

        return $dataProvider;
    }

}
