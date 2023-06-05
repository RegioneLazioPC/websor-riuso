<?php

namespace common\models\geo;

use Yii;
use common\models\geo\GeoLayer;

class GeoQuery extends \yii\db\ActiveRecord
{
    public static function queryTypes()
    {
        return [
            'INTERSEZIONE'=>'INTERSEZIONE',
            'INTERSEZIONE CON BUFFER'=>'INTERSEZIONE CON BUFFER',
            'PUNTO/AREA PIU VICINO'=>'PUNTO/AREA PIU VICINO',
            'N PUNTI/AREE PIU VICINE'=>'N PUNTI/AREE PIU VICINE',
        ];
    }
    public static function positions()
    {
        return [
            0=>'TAB',
            1=>'EVENTO',
            2=>'TAB, EVENTO'
        ];
    }
    public static function resultType()
    {
        return [
            'BOOLEAN'=>'BOOLEAN',
            'CAMPO DEL LAYER'=>'CAMPO DEL LAYER',
            'DISTANZA'=>'DISTANZA'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'geo_query';
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
            [[
                'name',
                'layer',
                'group',
                'query_type',
                'result_type',
                'layer_return_field',
            ],'string'],
            [['buffer', 'n_geometries', 'result_position'],'integer'],
            [['show_distance', 'enabled'], 'boolean'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Creazione',
            'updated_at' => 'Aggiornamento'
        ];
    }

    public function extraFields()
    {
        return ['layer'];
    }

    public function getLayer()
    {
        return $this->hasOne(GeoLayer::className(), ['layer_name'=>'layer']);
    }



    public function execQuery($evento)
    {
        $layer = $this->getLayer()->one();
        $layer_table_name = Yii::$app->params['geo_layer'] . "." . $layer->table_name;

        $need_group = false;
        $select = "";
        switch ($this->result_type) {
            case 'BOOLEAN':
                $select = "SELECT (count(ltb.*) > 0) as main_result";
                $need_group = true;
                break;
            case 'CAMPO DEL LAYER':
                $select = "SELECT \"".$this->layer_return_field."\" as main_result";
                break;
            case 'DISTANZA':
                $select = "SELECT ST_Distance( ST_Transform( ltb.".$layer->geometry_column.", 32632 ), ST_Transform( e.geom, 32632) ) as main_result";
                break;
        }

        if (in_array(strtoupper($layer->geometry_type), ['POLYGON','MULTIPOLYGON','LINESTRING','MULTILINESTRING'])) {
            $select .= ", st_aslatlontext(ST_Centroid(ST_Transform(ltb.".$layer->geometry_column.", 4326))) as deg, ST_X(ST_Centroid(ST_Transform(ltb.".$layer->geometry_column.", 4326))) AS lon, ST_Y(ST_Centroid(ST_Transform(ltb.".$layer->geometry_column.", 4326))) AS lat";
            //$select .= ", ST_X(ST_Centroid(ST_Transform(ltb.".$layer->geometry_column.", 3003))) AS s_lon, ST_Y(ST_Centroid(ST_Transform(ltb.".$layer->geometry_column.", 3003))) AS s_lat";
        }

        if (in_array(strtoupper($layer->geometry_type), ['POINT'])) {
            $select .= ", st_aslatlontext(ST_Transform(ltb.".$layer->geometry_column.", 4326)) as deg,
                            ST_X(ST_Transform(ltb.".$layer->geometry_column.", 4326)) AS lon,
                            ST_Y(ST_Transform(ltb.".$layer->geometry_column.", 4326)) AS lat";
            //$select .= ", ST_X(ST_Transform(ltb.".$layer->geometry_column.", 3003)) AS s_lon,
            //                ST_Y(ST_Transform(ltb.".$layer->geometry_column.", 3003)) AS s_lat";
        }

        $from = " from " . $layer_table_name . " ltb";
        $join = " left join utl_evento e ON e.id = :id_evento ";
        $where = "";

        switch ($this->query_type) {
            case 'INTERSEZIONE':
                if (!empty($this->buffer)) {
                    $where = "where ST_Intersects(
                        ST_Buffer( ST_Transform( ltb.".$layer->geometry_column.", 32632 ), ".$this->buffer."),
                        ST_Transform( e.geom, 32632 )
                    )";
                } else {
                    $where = "where ST_Contains( ST_Transform( ltb.".$layer->geometry_column.", 4326 ), e.geom)";
                }
                break;
            case 'INTERSEZIONE CON BUFFER':
                $where = "where ST_Intersects(
                    ST_Buffer( ST_Transform( ltb.".$layer->geometry_column.", 32632 ), ".$this->buffer."),
                    ST_Transform( e.geom, 32632 )
                )";
                break;
            case 'PUNTO/AREA PIU VICINO':
                if (!empty($this->buffer)) {
                    $where = "where ST_DWithin(
                        ST_Transform( ltb.".$layer->geometry_column.", 4326 )::geography,
                        (e.geom)::geography,
                        ".$this->buffer."
                    ) ORDER BY ST_Distance(ST_Transform( ltb.".$layer->geometry_column.", 4326 ), e.geom) ASC LIMIT 1";
                } else {
                    $where = "where 1 = 1 ORDER BY ST_Distance(ST_Transform( ltb.".$layer->geometry_column.", 4326 ), e.geom) ASC LIMIT 1";
                }
                break;
            case 'N PUNTI/AREE PIU VICINE':
                if (!empty($this->buffer)) {
                    $where = "where ST_DWithin(
                        ST_Transform( ltb.".$layer->geometry_column.", 4326 )::geography,
                        (e.geom)::geography,
                        ".$this->buffer."
                    ) ORDER BY ST_Distance(ST_Transform( ltb.".$layer->geometry_column.", 4326 ), e.geom) ASC LIMIT ".$this->n_geometries;
                } else {
                    $where = "where 1 = 1 ORDER BY ST_Distance(ST_Transform( ltb.".$layer->geometry_column.", 4326 ), e.geom) ASC LIMIT ".$this->n_geometries;
                }
                break;
        }

        if ($this->show_distance) {
            $select .= ", ST_Distance( ST_Transform( ltb.".$layer->geometry_column.", 32632 ), ST_Transform( e.geom, 32632) ) as distance_to_show";
        }

        $full_query = $select . $from . $join . $where;
        if ($need_group) {
            $full_query .= " GROUP BY ltb.geom, e.geom";
        }
        $results = Yii::$app->db->createCommand($full_query, [':id_evento'=>$evento->id])->queryAll();

        $array = $this->toArray([], [], true);
        $array['results'] = $results;
        return $array;
    }
}
