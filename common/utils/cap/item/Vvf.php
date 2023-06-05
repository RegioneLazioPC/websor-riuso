<?php 
namespace common\utils\cap\item;

use Yii;
use common\utils\cap\item\Standard as StandardCapFeed;
use common\utils\cap\item\ItemInterface;

class Vvf extends StandardCapFeed implements ItemInterface {

	public function __construct( $xml_data ) {
		parent::__construct($xml_data);
	}

	public function getScheda() {
		$s = explode(".", $this->identifier);
		return $s[count($s)-1];
	}

	public function getSchedaUpdate() {
		$s = explode(".", $this->identifier);
		return $s[count($s)-2];
	}

	public function getTipoEvento($n) {
		if(!isset($this->info[$n]['eventCodes'])) return null;

		return (isset($this->info[$n]['eventCodes']['Code_L1'])) ? $this->info[$n]['eventCodes']['Code_L1'] : null;

	}

	public function getSottoTipoEvento($n) {
		if(!isset($this->info[$n]['eventCodes'])) return null;

		return (isset($this->info[$n]['eventCodes']['Code_L2'])) ? $this->info[$n]['eventCodes']['Code_L2'] : null;
	}



	public function getCallTime($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['TIMECALL'])) ? $this->getDateFromFull($this->info[$n]['parameters']['TIMECALL']) : null;
	}

	public function getIntTime($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['TIMEINT'])) ? $this->getDateFromFull($this->info[$n]['parameters']['TIMEINT']) : null;
	}

	public function getArrivalTime($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['TIMEARR'])) ? $this->getDateFromFull($this->info[$n]['parameters']['TIMEARR']) : null;
	}

	public function getCloseTime($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['TIMECANC'])) ? $this->getDateFromFull($this->info[$n]['parameters']['TIMECANC']) : null;
	}

	public function getExpiresTime($n) {
		if(!isset($this->info[$n]['expires'])) return null;

		return $this->getDateFromFull($this->info[$n]['expires']);
	}

	public function getCodeInt($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['CODEINT'])) ? $this->info[$n]['parameters']['CODEINT'] : null;
	}

	public function getCodeCall($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['CODECALL'])) ? $this->info[$n]['parameters']['CODECALL'] : null;
	}

	public function getFormattedStatus($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['INCIDENTPROGRESS'])) ? $this->info[$n]['parameters']['INCIDENTPROGRESS'] : null;
	}

	public function getMajorEvent($n) {
		if(!isset($this->info[$n]['parameters'])) return null;

		return (isset($this->info[$n]['parameters']['MAJOREVENT'])) ? ($this->info[$n]['parameters']['MAJOREVENT'] == 'N' ? 0 : 1) : null;
	}

	public function getProfile() {
		return 'vvf';
	}

	/**
	 * Riferimento, es. evento
	 */
	protected function setReferences() {
		
		try {
			$references = explode(" ", $this->xml_data->references);
			foreach ($references as $referral) {
                $str = explode(",", $referral);
                if(isset($str[1])) $this->references[] = $str[1]; // prendo quello al centro
            }
		} catch(\Exception $e) {
			$this->references = [];
		}
	}

	/**
	 * Riferimento, es. evento
	 */
	protected function setIncidents() {
		
		try {
			$incidents = explode(" ", $this->xml_data->incidents);
			foreach ($incidents as $referral) {
                $str = explode(",", $referral);
                if(isset($str[1])) $this->incidents[] = $str[1]; // prendo quello al centro
            }
		} catch(\Exception $e) {
			$this->incidents = [];
		}
	}

	private function getDateFromFull( $date )
	{
		if($date instanceof \DateTime ) {
			$d = $date;
		} else {
			$d = \DateTime::createFromFormat("Y-m-d?H:i:sP", $date);
			if(is_bool($d)) return null;
		}

		return $d->format('Y-m-d H:i:sP');
	}

}
