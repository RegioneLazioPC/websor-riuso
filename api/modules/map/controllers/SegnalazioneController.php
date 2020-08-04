<?php

namespace api\modules\map\controllers;

use Exception;
use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use common\models\UtlSegnalazione;
use common\models\UtlOperatorePc;
use common\models\ConSegnalazioneExtra;
use common\models\ConEventoExtra;
use common\models\ConEventoSegnalazione;
use common\models\UtlAnagrafica;
use common\models\UtlUtente;

use common\models\UtlEvento;
use api\utils\ResponseError;

/**
 * Segnalazione Controller
 *
 */
class SegnalazioneController extends ActiveController
{
    public $modelClass = 'common\models\UtlSegnalazione';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] =[
                'class' => \api\utils\Authenticator::class,
                'except' => ['view','options']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['view','options'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create','update','change-position','attach-evento','change-to-evento'],
                    'roles' => ['@']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Di default per il metodo options torniamo ok in modo da non avere errori not found dalle chiamate automatiche del browser
     * @return [type] [description]
     */
    public function actionOptions() {
        return ['message'=>'ok'];
    }

    /**
     * Vedi segnalazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionView($id) {
        $segnalazione = UtlSegnalazione::find()->where(['id'=>$id])->with([
            'viewCartografia',
            'evento', 'evento.viewCartografia'
        ])->asArray()->one();

        if(!$segnalazione) ResponseError::returnSingleError(404, "Segnalazione non trovata");

        if(empty($segnalazione['evento'])) {
            $events = UtlEvento::find()
            ->select( ['*', 'ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) as distance'] )
            ->with(['tipologia','sottotipologia','comune','comune.provincia','viewCartografia'])
            ->where('ST_Distance_Sphere(geom, ST_MakePoint(:lon, :lat)) <= :distance')
            ->andWhere(['!=', 'stato', 'Chiuso'])
            ->addParams([
                ':lat' => floatval($segnalazione['lat']), 
                ':lon' => floatval($segnalazione['lon']), 
                ':distance' => intval(150000)
            ])
            ->orderBy(['distance'=>SORT_ASC])
            ->asArray()
            ->limit(300)
            ->all();
        } else {
            $events = [];
        }

        $segnalazione['eventi_vicini'] = $events;

        return $segnalazione;
    }

    /**
     * Modifica posizione segnalazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionChangePosition($id) {
        $segnalazione = UtlSegnalazione::findOne($id);
        if(!$segnalazione) ResponseError::returnSingleError(404, "Segnalazione non trovata");

        $segnalazione->lat = Yii::$app->request->post('lat');
        $segnalazione->lon = Yii::$app->request->post('lon');
        
        if(!$segnalazione->save()) ResponseError::returnMultipleErrors(422, $segnalazione->getErrors());

        return $segnalazione;
    }

    /**
     * Crea nuova segnalazione
     * @return [type] [description]
     */
    public function actionCreate( ) {

        $model = new UtlSegnalazione();
        $model->load(['UtlSegnalazione'=>Yii::$app->request->post()]);

        
        $model->stato = 'Nuova in lavorazione';

        $operatore = UtlOperatorePc::find()->where(['iduser' => Yii::$app->user->id])->one();
        if(isset($operatore)) $model->idsalaoperativa = $operatore->idsalaoperativa;

        $model->nome_segnalatore = @Yii::$app->request->post('nome');
        $model->cognome_segnalatore = @Yii::$app->request->post('cognome');
        $model->telefono_segnalatore = @Yii::$app->request->post('telefono');
        $model->email_segnalatore = @Yii::$app->request->post('email');

        if(!$model->save()) ResponseError::returnMultipleErrors(422, $model->getErrors());

        /*
        Useless anagrafica
        $anagrafica = new UtlAnagrafica();
        $anagrafica->load(['UtlAnagrafica'=>Yii::$app->request->post()]);
        $anagrafica = $anagrafica->createOrUpdate();

        if($anagrafica->getErrors()) :
            $model->delete();
            ResponseError::returnMultipleErrors(422, $anagrafica->getErrors());
        endif;

        $utente = new UtlUtente();
        $utente->load(['UtlUtente'=>Yii::$app->request->post()]);
        $utente->id_anagrafica = $anagrafica->id;
        
        if( !$utente->save(false) ){
            $model->delete();
            ResponseError::returnMultipleErrors(422, $utente->getErrors());
        }*/

        //$model->idutente = $utente->getPrimaryKey();        
        //if(!$model->save()) ResponseError::returnMultipleErrors(422, $model->getErrors());
        
        return $model;

    }

    /**
     * Trasforma segnalazione in evento
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionChangeToEvent($id) {
        
        $model = UtlSegnalazione::find()->where(['id'=>$id])->one();
        $data = $model->attributes;
        unset($data['stato']);

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {

            // Creo evento
            $eventoModel = new UtlEvento();
            $eventoModel->setAttributes($data);
            $eventoModel->stato = 'Non gestito';
            if(!$eventoModel->save(false)){
                ResponseError::returnMultipleErrors(422, $eventoModel->getErrors());
            }

            // Salvo gli extra segnalazione/evento
            $extras = $model->extras;
            if(!empty($extras)){

                foreach ($extras as $extra){
                    $eventoModel->link('extras', $extra);

                    $conSegnalazioneExtra = ConSegnalazioneExtra::find()->where(['idsegnalazione' => $model->id, 'idextra' => $extra->id])->one();
                    $conEventoExtra = ConEventoExtra::find()->where(['idevento' => $eventoModel->id, 'idextra' => $extra->id])->one();
                    
                    if(isset($extra)){
                        $dataEvento = $conSegnalazioneExtra->attributes;
                        $conEventoExtra->setAttributes($dataEvento);
                        $conEventoExtra->save();
                    }
                }
            }

            // Creo connessione con Segnalazione
            $conEventoSegnalazione = new ConEventoSegnalazione();
            $conEventoSegnalazione->idsegnalazione = $id;
            $conEventoSegnalazione->idevento = $eventoModel->id;
            $conEventoSegnalazione->save();
            if(!$conEventoSegnalazione->save(false)){
                ResponseError::returnMultipleErrors(422, $conEventoSegnalazione->getErrors());
            }

            // Cambio stato alla segnalazione
            $model->stato = 'Verificata e trasformata in evento';
            if(!$model->save(false)){
                ResponseError::returnMultipleErrors(422, $model->getErrors());
            }

            $dbTrans->commit();

        } catch (\Exception $e) {
            $dbTrans->rollBack();

            return ResponseError::returnSingleError(500, $e->getMessage());
        }

        return $model;
    }

    /**
     * Associa evento a segnalazione
     * @param  [type] $id       [description]
     * @param  [type] $idEvento [description]
     * @return [type]           [description]
     */
    public function actionAttachEvento($id, $idEvento)
    {
        $model = UtlSegnalazione::findOne($id);

        $conn = \Yii::$app->db;
        $dbTrans = $conn->beginTransaction();
        try {

            // Creo connessione con Segnalazione
            $conEventoSegnalazione = new ConEventoSegnalazione();
            $conEventoSegnalazione->idsegnalazione = $id;
            $conEventoSegnalazione->idevento = $idEvento;
            $conEventoSegnalazione->save();
            if(!$conEventoSegnalazione->save(false)){
                ResponseError::returnSingleError(422, $conEventoSegnalazione->getErrors());
            }

            // Cambio stato alla segnalazione
            $model->stato = 'Verificata e trasformata in evento';
            if(!$model->save(false)){
                ResponseError::returnSingleError(422, $model->getErrors());
            }

            $dbTrans->commit();

        }catch (Exception $e) {
            $dbTrans->rollBack();

            return ResponseError::returnSingleError( 500, $e->getMessage());
        }

        return $model;
    }


    

}
