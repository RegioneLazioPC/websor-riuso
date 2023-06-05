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

use common\models\UtlEvento;
use api\utils\ResponseError;
use common\models\ConOperatoreTask;
/**
 * Evento Controller
 */
class EventoController extends ActiveController
{
    public $modelClass = 'common\models\UtlEvento';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] =[
                'class' => \api\utils\Authenticator::class,
                'except' => ['options','view']
        ];

        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                throw new Exception('Non sei autorizzato', 401);
            },
            'except' => ['options','view'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create','update','change-position'],
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

   
    public function actionView($id) {
        $evento = UtlEvento::find()->where(['id'=>$id])->with([
            'viewCartografia',
            'genitore', 'genitore.viewCartografia',
            'segnalazioniAll', 'segnalazioniAll.viewCartografia',
            'fronti', 'fronti.viewCartografia', 
            'ingaggi', 
            'ingaggi.automezzo','ingaggi.automezzo.tipo',
            'ingaggi.attrezzatura','ingaggi.attrezzatura.tipo',
            'ingaggi.organizzazione',
            'ingaggi.sede'
        ])->asArray()->one();
        if(!$evento) ResponseError::returnSingleError(404, "Evento non trovato");

        return $evento;
    }

    /**
     * Modifica la posizione di un evento
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionChangePosition($id) {
        $evento = UtlEvento::findOne($id);
        if(!$evento) ResponseError::returnSingleError(404, "Evento non trovato");

        $diarioEvento = new ConOperatoreTask();
        //$diarioEvento->idfunzione_supporto = 1; //DATI CABLATI NEL DB
        $diarioEvento->idtask = 13; //DATI CABLATI NEL DB
        $diarioEvento->idevento = $evento->id;
        $diarioEvento->note = "Modifica posizione evento da " . $evento->lat . " - " . $evento->lon . " a " . Yii::$app->request->post('lat') . " - " . Yii::$app->request->post('lon');
        $diarioEvento->idoperatore = Yii::$app->user->identity->operatore->id;

        if(!($diarioEvento->save())) ResponseError::returnMultipleErrors(422, $diarioEvento->getErrors());

        $evento->lat = Yii::$app->request->post('lat');
        $evento->lon = Yii::$app->request->post('lon');
        $evento->is_public = (!$evento->is_public) ? 0 : $evento->is_public;
        if(!$evento->save()) ResponseError::returnMultipleErrors(422, $evento->getErrors());

        \common\models\cap\CapExposedMessage::generateFromEvent($evento, 'Change position');

        return $evento;
    }

    /**
     * Crea un nuovo evento
     * @return [type] [description]
     */
    public function actionCreate( ) {

        $model = new UtlEvento();
        $model->scenario = UtlEvento::SCENARIO_CREATE;
        
        $model->lat = Yii::$app->request->post('lat');
        $model->lon = Yii::$app->request->post('lon');
        $model->tipologia_evento = Yii::$app->request->post('tipologia_evento');
        $model->sottotipologia_evento = Yii::$app->request->post('sottotipologia_evento');
        if(Yii::$app->request->post('idparent')) $model->idparent = Yii::$app->request->post('idparent');

        if((!Yii::$app->request->post('luogo') || !Yii::$app->request->post('luogo') == '') && 
            (Yii::$app->request->post('indirizzo') || !Yii::$app->request->post('indirizzo') == '')) {

        }

        $model->luogo = (Yii::$app->request->post('luogo')) ? Yii::$app->request->post('luogo') : '';
        $model->indirizzo = (Yii::$app->request->post('indirizzo')) ? Yii::$app->request->post('indirizzo') : '';
        
        $model->idcomune = (!empty(Yii::$app->request->post('idcomune') ) && Yii::$app->request->post('idcomune') != '') ? Yii::$app->request->post('idcomune') : (Yii::$app->request->post('comune') ? Yii::$app->request->post('comune') : null);
        $model->stato = Yii::$app->request->post('stato');

        if(!$model->save()) ResponseError::returnMultipleErrors(422, $model->getErrors());

        return $model;

    }


    

}
