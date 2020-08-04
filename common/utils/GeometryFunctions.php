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

}