<?php

namespace common\components;

use common\models\LocComune;
use Yii;

/**
 * Filtra query e dati by $params['websorType']
 */
class FilteredActions extends \yii\base\Component
{
	/**
	 * @var string tipo websor
	 */
	public $type = 'regionale';

	public $show_footer_last_sync_mgo = false;
	public $view_sync_log = false;

	/**
	 * @var mixed comune
	 */
	public $comune = null;

	/**
	 * @var mixed provincia
	 */
	public  $provincia = null;

	/**
	 * @var boolean show field of type comune
	 */
	public $showFieldComune = true;

	/**
	 * @var boolean show field of type provincia
	 */
	public $showFieldProvincia = true;

	/**
	 * @var boolean show cartografico
	 */
	public $showCartografico = true;

	/**
	 * @var boolean show DOS
	 */
	public $showDos = true;

	/**
	 * @var boolean show DOS
	 */
	public $showElicottero = true;

	/**
	 * @var boolean show DOS
	 */
	public $showCanadair = true;

	// Init
	public function init()
	{
		if (isset(Yii::$app->params['websorType'])) {
			$this->type = Yii::$app->params['websorType'];
		}

		$this->view_sync_log = (isset(Yii::$app->params['view_sync_log'])) ? Yii::$app->params['view_sync_log'] : true;

		// INIT VARIABLES ENV FOR TYPE "comunale"
		if ($this->type == 'comunale') {
			// TO CHECK
			// IF MIGRATIONS WITH PARAMS AND NOT CREATED LOC_TABLE THERE WILL BE AN ERROR
			// WITH TRY CATCH WE PREVENT ERROR FOR FIRST MIGRATION
			try {
				$this->comune = LocComune::findOne(['codistat' => Yii::$app->params['websorCitiesIstat'][0]]);
			} catch(\Exception $e) {
				$this->comune = null;
			}
			$this->showFieldComune = false;
			$this->showFieldProvincia = false;
			//$this->showCartografico = false;
			$this->showDos = false;
			$this->showElicottero = false;
			$this->showCanadair = false;
			$this->show_footer_last_sync_mgo = true;
		}

		$this->showCartografico = (isset(Yii::$app->params['showCartography'])) ? Yii::$app->params['showCartography'] : false;

		parent::init();
	}

	/**
	 * @return boolean check if show field "comune" in form and table
	 */
	// public function showFieldComune()
	// {
	// 	if ($this->type == 'comunale') {
	// 		return false;
	// 	}
	// 	return true;
	// }

	// /**
	//  * @return boolean check if show field provincia
	//  */
	// public function showFieldProvincia()
	// {
	// 	if ($this->type == 'comunale') {
	// 		return false;
	// 	}
	// 	return true;
	// }

	public function getAppName()
	{
		if ($this->type == 'comunale' && !empty($this->comune)) {
			return $this->comune->comune;
		}

		return "REGIONE " . Yii::$app->params['REGION_NAME'];
	}
}
