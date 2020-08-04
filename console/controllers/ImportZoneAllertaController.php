<?php
namespace console\controllers;

use Exception;
use Yii;
use yii\console\Controller;
use yii\db\Migration;

use common\models\LocComune;
use common\models\AlmZonaAllerta;
use common\models\ConZonaAllertaComune;

use common\models\VolOrganizzazione;
use common\models\VolTipoOrganizzazione;
use common\models\ente\EntEnte;
use common\models\ente\EntTipoEnte;
use common\models\struttura\StrStruttura;
use common\models\struttura\StrTipoStruttura;

class ImportZoneAllertaController extends Controller
{
    public $migrate = null;

    private function normalize( $str ) {
        return preg_replace("/[^a-zA-Z0-9]/", "_", strtolower( $str ) );
    }

    /**
     * {@inheritdoc}
     */
    public function actionImport( $commit = 0 )
    {
        
        $file_name = 'zone_allerta.xlsx';

        $path = Yii::getAlias('@console');

        $file = $path . '/data/' . $file_name;

        if(!file_exists($file)) {
        	echo "File non presente \n";
        	return 0;
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile( $file );
        $reader->setReadDataOnly(true);

        $worksheet = $reader->load( $file );

        $conn = Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        try {

	        $this->parseWorksheet( $worksheet );
	        if($commit == 1) {
	        	$dbTrans->commit();
	        } else {
	        	$dbTrans->rollBack();
	        }

	    } catch(\Exception $e){
	    	
	    	$dbTrans->rollBack();
	    	throw $e;
	    	
	    }
                

    }

    protected function parseWorksheet( $worksheet ) {
    	for($s_n = 0; $s_n < $worksheet->getSheetCount(); $s_n++) {
        	$worksheet->setActiveSheetIndex( $s_n );
        	$data = $worksheet->getActiveSheet()->toArray(null, false, true, false);	

        	foreach ($data as $row) {
        		
        		if($this->isLastRow($row)) break;

        		$this->parseExcelRow( $row );
        	}

        }
    }

    private function parseExcelRow( array $row ) {
    	
    	if(!$this->isValidRow($row)) return;

    	foreach ($this->getZones($row[2]) as $zona) $this->linkZonaComune( $this->getComune( $row[0]), $this->getSplittedZone( $zona ) );

    }

    private function linkZonaComune( LocComune $comune, array $zona ) 
    {
    	$_zona = AlmZonaAllerta::find()
    	->where(['code' => $zona[0]])
    	->andWhere(['nome'=>$zona[1]])
    	->one();
    	if(!$_zona) $_zona = new AlmZonaAllerta;

    	$_zona->code = $zona[0];
    	$_zona->nome = $zona[1];
    	if(!$_zona->save()) var_dump($_zona->getErrors());

    	$conn = ConZonaAllertaComune::find()
    	->where(['codistat_comune'=>$comune->codistat])
    	->andWhere(['id_alm_zona_allerta'=>$_zona->id])
    	->one();

    	if(!$conn) {
    		$comune->link('zoneAllerta', $_zona);
    	}

    	echo $comune->comune . ": " . $_zona->code . " - " . $_zona->nome . "\n";

    }

    private function getSplittedZone( string $zona ) : array {
    	$z = explode("-", $zona);
    	$z[0] = trim($z[0]);
    	$z[1] = trim($z[1]);
    	return $z;
    }


    private function getZones( string $zone ) : array {
    	$_zone = explode(",", $zone);
    	return array_map(function($zona) { return trim($zona); }, $_zone);
    }

    private function isValidRow( array $row ) : bool {
    	return (!empty($row) && $row[0] != 'COMUNE') ? true : false;
    }

    private function isLastRow( array $row ) : bool {
    	return (empty($row[0]) || $row[0] == '') ? true : false;
    }

    private function parseComune( string $comune ) : string {
    	$comune = str_replace("(i.a.)", "", $comune);
    	$comune = str_replace("(i.a. 1)", "", $comune);
    	$comune = str_replace("(i.a. 2)", "", $comune);
    	return trim( $comune );
    }

    private function getComune( string $comune ) : LocComune {

    	$comune = $this->parseComune( $comune );

    	$c = LocComune::find()->where(['=','comune', $comune])->andWhere(['id_regione'=>12])->all();
    	if(!$c || count($c) > 1) throw new \Exception("Comune " . $comune . " non trovato o duplicato", 1);
    	
    	return $c[0];
    }

    /**
     * Imposta le zone di allerta in base alla configurazione
     *
     * ricordare di disabilitare la sincronizzazione con Everbridge prima di lanciare il comando
     *
     * ./yii import-zone-allerta/set-zone-allerta 1
     * @return void
     */
    public function actionSetZoneAllerta( $commit = 0 ) {

        $conn = Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        try{
    	
        	$enti = EntEnte::find()->all();
        	$struttura = StrStruttura::find()->all();
        	$organizzazione = VolOrganizzazione::find()->all();

        	$tipo_ente = EntTipoEnte::find()->all();
        	$tipo_struttura = StrTipoStruttura::find()->all();
        	$tipo_organizzazione = VolTipoOrganizzazione::find()->all();

            foreach ($tipo_struttura as $t_s) {
                $t_s->updateChildrenZoneAllerta();
            }

            foreach ($tipo_ente as $t_e) {
                $t_e->updateChildrenZoneAllerta();
            }

            foreach ($tipo_organizzazione as $t_o) {
                $t_o->updateChildrenZoneAllerta();
            }

            $prefetture = EntTipoEnte::find()->where(['ilike','descrizione','prefettura'])->one();
            $prefetture->update_zona_allerta_strategy = 1;
            $prefetture->save();

            if($commit == 1) {
                $dbTrans->commit();
            } else {
                $dbTrans->rollBack();
            }

        } catch( \Exception $e ) {
            $dbTrans->rollBack();
            throw $e;
        }

    }


}