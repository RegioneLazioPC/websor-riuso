<?php

namespace common\models\geo;

use Yii;

class GeoLayer extends \yii\db\ActiveRecord
{
    public $shapefile;
    public $srid;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'geo_layer';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['fields']
            ],
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
            ]
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['layer_name', 'shapefile_name', 'geometry_type', 'table_name', 'geometry_column'], 'string'],
            [['srid'], 'required'],
            [['srid'],'integer'],
            [['fields', 'shapefile'], 'safe'],
            [['layer_name'], 'required'],
            [['layer_name'], 'unique'],
            [['layer_name'], 'string', 'max'=>59]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'layer_name' => 'Layer',
            'shapefile_name' => 'Shape',
            'fields' => 'Campi',
            'created_at' => 'Creazione',
            'updated_at' => 'Aggiornamento'
        ];
    }

    public function extraFields()
    {
        return [];
    }
}
