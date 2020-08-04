<?php
namespace console\controllers;

use Exception;
use Yii;
use yii\console\Controller;

/**
 * Operazioni one shot di pulizia
 */
class CleanController extends Controller
{   
    /**
     * Spostamento dei type in tabella di connessione per utilizzo stesso contatto con finalità diverse
     *
     * @deprecated (One Shot Script)
     * @param  integer $commit [description]
     * @return [type]          [description]
     */
    public function actionUpdateContattiTypeInConnection($commit = 0) 
    {
        $connection = Yii::$app->getDb();
        $dbTrans = $connection->beginTransaction();

        $command = $connection->createCommand("
            UPDATE con_organizzazione_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();
        
        $command = $connection->createCommand("
            UPDATE con_sede_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();

        $command = $connection->createCommand("
            UPDATE con_ente_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();
        
        $command = $connection->createCommand("
            UPDATE con_ente_sede_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();

        $command = $connection->createCommand("
            UPDATE con_struttura_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();
        
        $command = $connection->createCommand("
            UPDATE con_struttura_sede_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();

        $command = $connection->createCommand("
            UPDATE con_anagrafica_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();

        $command = $connection->createCommand("
            UPDATE con_mas_rubrica_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();

        $command = $connection->createCommand("
            UPDATE con_operatore_pc_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();
        
        $command = $connection->createCommand("
            UPDATE con_volontario_contatto SET type = (SELECT type FROM utl_contatto WHERE id = id_contatto)
        ")->execute();

        

        if($commit == 1) {
            $dbTrans->commit();
        } else {
            $dbTrans->rollBack();
        }
        
    }


}