<?php

namespace api\modules\snc\controllers;

use Yii;
use yii\web\Controller;
use common\components\Syncer;
use yii\rest\ActiveController;


/**
 * 
 * @author Fabio Rizzo
 */
class SyncController extends Controller
{
    /*
        public $modelClass = 'common\models\UtlSegnalazione';

        public function actions()
        {
            $actions = parent::actions();
            unset($actions['index']);
            unset($actions['view']);
            unset($actions['update']);
            unset($actions['delete']);
            unset($actions['create']);
            return $actions;
        }
    */


    private $actions = [
        'updated_organizzazione',
        'created_organizzazione',
        'deleted_organizzazione',
        'updated_ente',
        'created_ente',
        'deleted_ente',
        'updated_struttura',
        'created_struttura',
        'deleted_struttura',

        'updated_risorsa',
        'created_risorsa',
        'deleted_risorsa',

        'updated_sede',
        'created_sede',
        'deleted_sede',
        'updated_ente_sede',
        'created_ente_sede',
        'deleted_ente_sede',
        'updated_struttura_sede',
        'created_struttura_sede',
        'deleted_struttura_sede',


        'updated_volontario',
        'created_volontario',
        'deleted_volontario',
        'updated_anagraficavolontario',
        'updated_anagrafica',
        'created_anagrafica',
        'deleted_anagrafica',
        'updated_tipoOrganizzazione',
        'created_tipoOrganizzazione',
        'deleted_tipoOrganizzazione',
        'updated_tipoRisorsa',
        'created_tipoRisorsa',
        'deleted_tipoRisorsa',
        'updated_tipoRisorsaMeta',
        'created_tipoRisorsaMeta',
        'deleted_tipoRisorsaMeta',

        'updated_specializzazione',
        'created_specializzazione',
        'deleted_specializzazione',

        'updated_sezioneSpecialistica',
        'created_sezioneSpecialistica',
        'deleted_sezioneSpecialistica',

        'updated_ruoloVolontario',
        'created_ruoloVolontario',
        'deleted_ruoloVolontario',
        
        'deleted_indirizzo',
        'deleted_contatto'
    ];

    public function beforeAction($action) {
        
        $request = Yii::$app->getRequest();
        try {
            
            $username = $request->getHeaders()->get('user');
            $pwd = $request->getHeaders()->get('pwd');

            if($username == Yii::$app->params['sync_credentials']['user'] && 
                $pwd == Yii::$app->params['sync_credentials']['pwd'] ) {
                return true;
            }

            Yii::info("Error sync with api: invalid auth", 'sync');
            throw new \yii\web\HttpException(403, "Non sei autorizzato");

        } catch ( \Exception $e ) {
            Yii::info("Error sync with api: invalid auth exception", 'sync');
            throw $e;
        }
        return parent::beforeAction($action);
    }

    public function actionAdd() {
        Yii::error( 'start sync', 'sync' );
        //Yii::info("Start sync with api: ".Yii::$app->request->post('datas'), 'sync');
        $data = unserialize( Yii::$app->request->post('datas') );
        
        if(in_array($data['action'], $this->actions)) :
            try {
                
                call_user_func( array( new Syncer($data), $data['action'] ) );
            } catch ( \Exception $e ) {
                Yii::error((string) $e, 'sync');
                Yii::info("Error sync with api: ".$e->getMessage(), 'sync');
                throw $e;
            }
        else:
            Yii::info("Sync with api invalid action: ".$data['action'], 'sync');
        endif;

        //Yii::info("Ok sync with api", 'sync');
        return ['ok'];
    }
    
}


