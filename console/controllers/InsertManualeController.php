<?php 
namespace console\controllers;

use Yii;
use yii\console\Controller;

use common\models\VolOrganizzazione;
use common\models\VolSede;
use common\models\UtlAutomezzo;
use common\models\UtlAttrezzatura;

class InsertManualeController extends Controller {
	
	/**
	 * Inserisci manualmente una organizzazione
	 * ./yii insert-manuale/add-organizzazione 3 "Nome Organizzazione" "123456789" "123456789" 
	 * 9 elisuperficie
	 * @return [type] [description]
	 */
	public function actionAddOrganizzazione($id_tipo, $nome, $codfiscale, $piva) 
	{
		$org = new VolOrganizzazione();
		$org->codicefiscale = $codfiscale;
		$org->partita_iva = $piva;
		$org->denominazione = $nome;
		$org->id_tipo_organizzazione = $id_tipo;
		if(!$org->save()) :
			var_dump($org->getErrors());
			echo "\n";
		endif;

		var_dump($org->attributes);
		echo "\n";
	}

	/**
	 * Crea una sede manualmente
	 * ./yii insert-manuale/add-sede 3652 "Via di esempio 01" 4841 "Sede Legale" "sample@mailinator.com" "3382324925" "3382324925" "00100" "" "42.089940031278" "12.511062689831" 
	 * 
	 * @param  [type] $organizzazione [description]
	 * @param  [type] $indirizzo      [description]
	 * @param  [type] $id_comune      [description]
	 * @param  [type] $tipo           [description]
	 * @param  [type] $email          [description]
	 * @param  [type] $telefono       [description]
	 * @param  [type] $altro_telefono [description]
	 * @param  [type] $cap            [description]
	 * @param  [type] $altro_fax      [description]
	 * @param  [type] $lat            [description]
	 * @param  [type] $lon            [description]
	 * @return [type]                 [description]
	 */
	public function actionAddSede($organizzazione, $indirizzo, $id_comune, $tipo, $email, $telefono, $altro_telefono, $cap, $altro_fax, $lat, $lon)
	{
		$sede = new VolSede();
		$sede->id_organizzazione = $organizzazione;
		$sede->indirizzo = $indirizzo;
		$sede->comune = $id_comune;
		$sede->tipo = $tipo;
		$sede->email = $email;
		$sede->telefono = $telefono;
		$sede->altro_fax = $altro_fax;
		$sede->altro_telefono = $altro_telefono;
		$sede->cap = $cap;
		$sede->lat = $lat;
		$sede->lon = $lon;
		if(!$sede->save()) :
			var_dump($sede->getErrors());
			echo "\n";
		endif;
		var_dump($sede->attributes);
		echo "\n";
	}

	/**
	 * Inserisci manualmente un automezzo
	 * ./yii insert-manuale/add-automezzo 50 "ELI01" 3652 7447 "AS908UI"
	 * @param  [type] $tipo           [description]
	 * @param  [type] $modello        [description]
	 * @param  [type] $organizzazione [description]
	 * @param  [type] $sede           [description]
	 * @param  [type] $targa          [description]
	 * @return [type]                 [description]
	 */
	public function actionAddAutomezzo($tipo, $modello, $organizzazione, $sede, $targa)
	{
		$automezzo = new UtlAutomezzo();
		$automezzo->idtipo = $tipo;
		$automezzo->modello = $modello;
		$automezzo->idorganizzazione = $organizzazione;
		$automezzo->idsede = $sede;
		$automezzo->targa = $targa;
		if(!$automezzo->save()) :
			var_dump($automezzo->getErrors());
			echo "\n";
		endif;
		var_dump($automezzo->attributes);
		echo "\n";
	}


}