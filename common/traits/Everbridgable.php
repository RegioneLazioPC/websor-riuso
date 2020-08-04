<?php 
namespace common\traits;

use Yii;
use common\utils\EverbridgeUtility;

/**
 * Si aggancia ai model per aggiornare il record su everbridge
 */
trait Everbridgable {

	public function syncEverbridge() {
		
		if ( Yii::$app->params['sync_everbridge'] ) {	
			EverbridgeUtility::updateSingleContact( $this->getEverbridgeIdentifier() );
		} else {
			return;
			//throw new \Exception("Everbridge non sincronizzato", 1);
		}
	}

	/**
	 * Usa utility per cancellare gli external id
	 * poi elimina i record da tabella di relazione
	 * @return [type] [description]
	 */
	public function removeFromEverbridge() {
		if ( Yii::$app->params['sync_everbridge'] ) {
			
			$connection = Yii::$app->getDb();
			$command = $connection->createCommand("
			    SELECT ext_id FROM con_view_rubrica_everbridge_ext_ids WHERE identificativo = :id
				", [ ':id' => $this->getEverbridgeIdentifier() ]);

			$result = $command->queryAll();

			$ext_ids = [];
			foreach ($result as $row) {
				$ext_ids[] = $row['ext_id'];
			}
			// se era già cancellata lascia stare
			if(count($ext_ids) > 0) {
				EverbridgeUtility::deleteExtIds( array_unique($ext_ids) );

				$command = $connection->createCommand("
				    DELETE FROM con_view_rubrica_everbridge_ext_ids WHERE identificativo = :id
					", [ ':id' => $this->getEverbridgeIdentifier() ]);

				$result = $command->queryAll();
			}

		}

	}

	/**
	 * Recupera gli ext_id del model
	 * @return [type] [description]
	 */
	public function getExtIds() {
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand("
		    SELECT ext_id FROM con_view_rubrica_everbridge_ext_ids WHERE identificativo = :id
			", [ ':id' => $this->getEverbridgeIdentifier() ]);

		$result = $command->queryAll();

		$ext_ids = [];
		foreach ($result as $row) {
			$ext_ids[] = $row['ext_id'];
		}

		return array_unique( $ext_ids );
	}

}