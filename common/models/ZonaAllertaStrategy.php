<?php 

namespace common\models;

class ZonaAllertaStrategy {

	private $strategies = [
		0 => 'Comune sede',
		1 => 'Provincia sede',
		2 => 'Tutte',
		3 => 'Manuale'
	];

	public static function getZonaManuale() {
		return 3;
	}

	public static function getStrategyLabel( ?int $index ) : string {
		if(!$index) $index = 0;
		return (new self)->strategies[$index];
	}

	public static function getStrategies() : array {
		return (new self)->strategies;
	}

}