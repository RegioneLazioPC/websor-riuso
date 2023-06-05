<?php 
namespace common\utils;

use Yii;
use common\models\LocComune;

class GeometryFunctions{
	
	/**
	 * Verifica che le coordinate rientrino all'interno del comune di riferimento
	 * 
	 * Utilizza la tabella loc_comune_geom per il match con loc_comune
	 * 
	 * @param  [float] $lat       Latitudine
	 * @param  [float] $lon       Longitudine
	 * @param  [integer] $id_comune Id comune nella tabella loc_comune
	 * @return [boolean]          true se rientra nella geometria, false in caso contrario
	 */
	public static function verifyLatLonInComune( $lat, $lon, $id_comune ) {
		/**
         * Seleziono il comune dal database
         * @var \common\models\locations\LocComune
         */
        $comune = LocComune::findOne( $id_comune );
        if ( !$comune ) throw new Exception("Comune non valido", 1);
        
        /**
         * Verifico che latitudine e longitudine inserite rientrino all'interno della geometria del comune
         * il match tra le tabelle loc_comune (quella di base) e loc_comune_geom (quella geometrica) è tra pro_com e codistat
         * @var [type]
         */
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT pro_com
            FROM
            loc_comune_geom
            WHERE 
             ST_DWithin(geom, ST_Transform(ST_SetSRID(ST_Point(:lon, :lat),4326), 32632 ), 3)
             AND loc_comune_geom.pro_com::TEXT = :codistat::TEXT
             LIMIT 1", [ ':lon' => $lon, ':lat' => $lat, ':codistat' => $comune->codistat ]);

        $result_ = $command->queryAll();
        // se non ho risultati lat e lon non rientrano nella geometria
        return ( count($result_) ) <= 0 ? false : true;
        
	}

    /**
     * Prendi comune corretto
     * @param  [type] $lat [description]
     * @param  [type] $lon [description]
     * @return [type]      [description]
     */
    public static function getComuneByLatLon($lat, $lon) {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT *
            FROM
            loc_comune_geom
            WHERE 
             ST_DWithin(geom, ST_Transform(ST_SetSRID(ST_Point(:lon, :lat),4326), 32632 ), 3)
             LIMIT 1", [ ':lon' => $lon, ':lat' => $lat ]);

        $result = $command->queryAll();

        return (count($result) > 0) ? $result[0] : false;
    }

    public static function getCentroid($codistat) {
        
        $centroid = Yii::$app->db->createCommand("SELECT
        ST_X( ST_Centroid( ST_Transform(ST_SetSRID(geom,32632), 4326) )::geometry) as lon,
        ST_Y( ST_Centroid( ST_Transform(ST_SetSRID(geom,32632), 4326) )::geometry) as lat
        FROM loc_comune_geom WHERE pro_com = :codistat;", [':codistat'=>(int) $codistat])->queryAll();

        if(count($centroid) < 1) return null;
        
        return [
            'lat'=>$centroid[0]['lat'],
            'lon'=>$centroid[0]['lon']
        ];
    }

}