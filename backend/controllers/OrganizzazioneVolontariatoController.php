<?php

namespace backend\controllers;

use Yii;
use common\models\VolOrganizzazione;
use common\models\VolOrganizzazioneSearch;
use common\models\VolSede;
use common\models\VolSedeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use common\models\LocComune;
use Exception;
use yii\data\ActiveDataProvider;
/**
 * VolOrganizzazioneController implements the CRUD actions for VolOrganizzazione model.
 */
class OrganizzazioneVolontariatoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if(Yii::$app->user){
                        Yii::error("Tentativo di accesso non autorizzato organizzazioni user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();                        
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['viewOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'permissions' => ['createOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['updateOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['deleteOrganizzazione']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-sede'],
                        'permissions' => ['createSede']
                    ],
                ]        
            ]    
        ];
    }

    /**
     * Lists all VolOrganizzazione models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VolOrganizzazioneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VolOrganizzazione model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new VolSedeSearch();
        
        // formatto le query string per la ricerca delle sedi
        // di base l'id_organizzazione è l'id della vista corrente
        $params = ( isset(Yii::$app->request->queryParams['VolSedeSearch']) ) ? 
        ['VolSedeSearch'=> array_merge(Yii::$app->request->queryParams['VolSedeSearch'],['id_organizzazione'=>$id])] : 
        ['VolSedeSearch'=>['id_organizzazione'=>$id]];
        $dataProvider = $searchModel->search($params);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new VolOrganizzazione model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VolOrganizzazione();
        

        if(Yii::$app->request->method == 'POST') :
            $datas = Yii::$app->request->post();  

            try {
                // formatto le date per postgresql     
                $data_albo_regionale = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_albo_regionale']);
                $datas['VolOrganizzazione']['data_albo_regionale'] = $data_albo_regionale->format('Y-m-d H:i:s');

                $data_scadenza_assicurazione = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_scadenza_assicurazione']);
                $datas['VolOrganizzazione']['data_scadenza_assicurazione'] = $data_scadenza_assicurazione->format('Y-m-d H:i:s');  

                $model->load($datas);
                if(!$model->validate()) throw new \Exception("Errore salvataggio", 1);
                

                if($datas['VolOrganizzazione']['manual_zona_update'] && $datas['VolOrganizzazione']['manual_zona_update'] == 1) {
                    $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = \common\models\ZonaAllertaStrategy::getZonaManuale();
                    $datas['VolOrganizzazione']['zone_allerta'] = implode(",",$datas['VolOrganizzazione']['zone_allerta_array']);
                } else {
                    $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = $model->tipoOrganizzazione->update_zona_allerta_strategy;
                }

                $model->load($datas); 
                if(!$model->save()) throw new \Exception("Errore salvataggio", 1);

                return $this->redirect(['view', 'id' => $model->id]); 

            } catch(\Exception $e){
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            
        endif;
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Crea una nuova sede per organizzazione di volontariato
     * @return mixed
     */
    public function actionCreateSede($id)
    {
        $model = new VolSede();        

        if(Yii::$app->request->method == 'POST') :
            try {
                $datas = Yii::$app->request->post(); 
                $datas['VolSede']['id_organizzazione'] = $id;
                if(empty($datas['VolSede']['lat']) || empty($datas['VolSede']['lon'])) :

                    $model->load($datas);
                    if(!$model->validate()) throw new Exception("Dati non validi");

                    $comune = LocComune::findOne($datas['VolSede']['comune']);
                    $address = $datas['VolSede']['indirizzo']." ".
                    $comune->comune." (".
                    $comune->provincia->sigla.")";

                    $lat_lng = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&key=".Yii::$app->params['google_key']);
                    $res = json_decode($lat_lng, true);

                    if(
                        isset($res['results']) && 
                        isset($res['results'][0]) && 
                        isset($res['results'][0]['geometry']) && 
                        isset($res['results'][0]['geometry']['location']) && 
                        isset($res['results'][0]['geometry']['location']['lng']) 
                    ) :

                        $datas['VolSede']['lat'] = $res['results'][0]['geometry']['location']['lat'];
                        $datas['VolSede']['lon'] = $res['results'][0]['geometry']['location']['lng'];

                    else:
                        throw new Exception("Errore nella ricerca delle coordinate");
                    endif;
                endif;

                

                $model->load($datas);
                if(!$model->save()) throw new \Exception($model->getErrors(), 1);
                ;

                return $this->redirect(['view', 'id' => $id]);

            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('create-sede', [
                    'model' => $model
                ]);
            }
        endif;
        
        return $this->render('create-sede', [
            'model' => $model,
            'id' => $id
        ]);

    }

    /**
     * Updates an existing VolOrganizzazione model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->method == 'POST') :

            $datas = Yii::$app->request->post();   

            if( !empty($model->id_sync) ) {
            
                $model->scenario = VolOrganizzazione::SCENARIO_UPDATE_SYNCED;
            
            } else {

                // formatto le date per postgresql            
                $data_albo_regionale = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_albo_regionale']);

                $datas['VolOrganizzazione']['data_albo_regionale'] = $data_albo_regionale->format('Y-m-d H:i:s');

                $data_scadenza_assicurazione = \DateTime::createFromFormat('m-d-Y', $datas['VolOrganizzazione']['data_scadenza_assicurazione']);

                $datas['VolOrganizzazione']['data_scadenza_assicurazione'] = $data_scadenza_assicurazione->format('Y-m-d H:i:s');  


            }

            if($datas['VolOrganizzazione']['manual_zona_update'] && $datas['VolOrganizzazione']['manual_zona_update'] == 1) {
                $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = \common\models\ZonaAllertaStrategy::getZonaManuale();
                $datas['VolOrganizzazione']['zone_allerta'] = implode(",",$datas['VolOrganizzazione']['zone_allerta_array']);
            } else {
                $datas['VolOrganizzazione']['update_zona_allerta_strategy'] = $model->tipoOrganizzazione->update_zona_allerta_strategy;
            }

            
            if($model->load($datas) && $model->save()) return $this->redirect(['view', 'id' => $model->id]);
            
        endif;

        
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing VolOrganizzazione model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the VolOrganizzazione model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VolOrganizzazione the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VolOrganizzazione::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
