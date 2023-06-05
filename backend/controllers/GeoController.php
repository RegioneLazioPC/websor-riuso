<?php

namespace backend\controllers;

use Yii;
use common\models\geo\GeoLayer;
use common\models\geo\GeoLayerSearch;
use common\models\geo\GeoQuery;
use common\models\geo\GeoQuerySearch;
use common\models\geo\DynamicLayerData;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use ZipArchive;

use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;
use yii\base\DynamicModel;

class GeoController extends Controller
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
                    if (Yii::$app->user) {
                        Yii::error("Tentativo di accesso non autorizzato user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['ListGeoLayer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create','create-from-table'],
                        'permissions' => ['CreateGeoLayer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['DeleteGeoLayer']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index-query'],
                        'permissions' => ['ListGeoQuery']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-query'],
                        'permissions' => ['CreateGeoQuery']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-query'],
                        'permissions' => ['UpdateGeoQuery']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['list-layer-fields'],
                        'permissions' => ['ListGeoQuery','CreateGeoQuery','UpdateGeoQuery']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-query'],
                        'permissions' => ['DeleteGeoQuery']
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all GeoLayer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GeoLayerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single GeoLayer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $fields = array_keys($model->fields);
        unset($fields[$model->geometry_column]);

        $q_params = isset(Yii::$app->request->queryParams['DynamicLayerData']) ? Yii::$app->request->queryParams['DynamicLayerData'] : [];
        $searchModel = new DynamicLayerData();
        $searchModel->rules = [
            [$fields, 'string']
        ];

        $dataProvider = $searchModel->search($model->table_name, Yii::$app->request->queryParams);


        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Creates a new GeoLayer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GeoLayer();

        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $shape = UploadedFile::getInstance($model, 'shapefile');
                if (empty($shape)) {
                    throw new \Exception("Carica uno shapefile in archivio zip", 1);
                }


                $file_path = Yii::getAlias('@backend/uploads');
                $filename = $file_path.'/shapefiles/'.$shape->getBaseName().'.zip';

                if (!file_exists($file_path)) {
                    mkdir($file_path);
                }
                if (!file_exists($file_path.'/shapefiles')) {
                    mkdir($file_path.'/shapefiles');
                }
                $shape->saveAs($filename);

                $table_name = strtolower(preg_replace('/[^\da-z]/i', '', $model->layer_name));

                $zip = new ZipArchive();
                if ($zip->open($filename) === true) {
                    if (is_dir($file_path.'/shapefiles/'.$table_name)) {
                        throw new \Exception("Esiste già un layer con questo nome", 1);
                    }
                        
                    mkdir($file_path.'/shapefiles/'.$table_name);
                    
                    $zip->extractTo($file_path.'/shapefiles/'.$table_name);
                    $zip->close();

                    $shape_file = new ShapefileReader($file_path.'/shapefiles/'.$table_name.'/'.$shape->getBaseName(), [
                        Shapefile::OPTION_IGNORE_FILE_DBF,
                        Shapefile::OPTION_IGNORE_FILE_SHX,
                        Shapefile::OPTION_DBF_CONVERT_TO_UTF8
                    ]);
                    $shape_file->setCharset('utf-8');

                    $columns = [];
                    foreach ($shape_file->getFields() as $key => $field) {
                        $column_name = strtolower($key);
                        $columns[$column_name] = $this->getColumnTypeByField($field);
                    }
                    $columns['geom'] = $this->getGeometryType($shape_file);

                    Yii::$app->db->createCommand("DROP TABLE IF EXISTS ".Yii::$app->params['geo_layer'].'.'.$table_name)->execute();
                    Yii::$app->db->createCommand()->createTable(Yii::$app->params['geo_layer'].'.'.$table_name, $columns, null)->execute();


                    $query_cols = "SELECT 
                       column_name, 
                       data_type
                    FROM 
                       information_schema.columns
                    WHERE 
                       table_name = :tname and table_schema = :tschema;";

                    $all_cols = Yii::$app->db->createCommand($query_cols, [':tschema'=>Yii::$app->params['geo_layer'], ':tname'=>$table_name])->queryAll();

                    $cls = [];
                    foreach ($all_cols as $column) {
                        if ($column['column_name'] == 'geom') {
                            $cls['geom'] = $this->getGeometryTypePlain($shape_file);
                        } else {
                            $cls[$column['column_name']] = strtoupper($column['data_type']);
                        }
                    }




                    $model->fields = $cls;
                    $model->table_name = $table_name;
                    $model->geometry_type = $this->getGeometryTypePlain($shape_file);
                    $model->geometry_column = 'geom';
                    $model->shapefile_name = $shape->getBaseName();


                    while ($geometry = $shape_file->fetchRecord()) {
                        $arr = [];
                        foreach ($geometry->getDataArray() as $key => $value) {
                            $arr[strtolower($key)] = $value;
                        }
                        $arr['geom'] = new \yii\db\Expression('ST_GeomFromText(\''.$geometry->getWKT().'\', '.$model->srid.')');
                        //$arr['geom'] = new \yii\db\Expression('ST_GeomFromGeoJSON(\''.$geometry->getGeoJSON().'\')');

                        Yii::$app->db->createCommand()->insert(Yii::$app->params['geo_layer'].'.'.$table_name, $arr)->execute();
                    }
                } else {
                    throw new \Exception("Errore nell'estrazione dell'archivio", 1);
                }


                if (!$model->save()) {
                    throw new \Exception("Errore nel salvataggio del layer " . json_encode($model->getErrors()), 1);
                }


                $tr->commit();
            } catch (\Exception $e) {
                $tr->rollBack();
                throw $e;
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionCreateFromTable()
    {
        $model = new DynamicModel(['layer_name', 'table_name','srid']);
        $model->addRule(['layer_name', 'table_name', 'srid'], 'required')
            ->addRule(['layer_name', 'table_name'], 'string', ['max' => 59])
            ->addRule(['srid'], 'integer');

        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db->beginTransaction();

            try {
                $table_name = $model->table_name;
                $layer_name = $model->layer_name;
                $srid = $model->srid;

                $geolayer = new GeoLayer();
                $geolayer->table_name = $table_name;
                $geolayer->layer_name = $layer_name;
                $geolayer->srid = $srid;

                // columns
                $query_cols = "SELECT 
                       column_name, 
                       data_type
                    FROM 
                       information_schema.columns
                    WHERE 
                       table_name = :tname and table_schema = :tschema;";

                $query_geom = "SELECT f_geometry_column, \"type\"  
                        FROM geometry_columns 
                        WHERE f_table_schema = :tschema
                        AND f_table_name = :tname;";


                $geometry_cols = Yii::$app->db->createCommand($query_geom, [':tschema'=>Yii::$app->params['geo_layer'], ':tname'=>$table_name])->queryAll();
                if (count($geometry_cols) == 0) {
                    throw new \Exception("La tabella non ha colonne geometriche", 1);
                }
                if (count($geometry_cols) > 1) {
                    throw new \Exception("La tabella non può avere più di 1 colonna geometrica", 1);
                }


                $all_cols = Yii::$app->db->createCommand($query_cols, [':tschema'=>Yii::$app->params['geo_layer'], ':tname'=>$table_name])->queryAll();

                $columns = [];
                foreach ($all_cols as $column) {
                    if ($column['column_name'] == $geometry_cols[0]['f_geometry_column']) {
                        $columns[$column['column_name']] = $geometry_cols[0]['type'];
                    } else {
                        $columns[$column['column_name']] = strtoupper($column['data_type']);
                    }
                }

                $geolayer->fields = $columns;
                $geolayer->table_name = $table_name;
                $geolayer->geometry_type = $geometry_cols[0]['type'];
                $geolayer->geometry_column = $geometry_cols[0]['f_geometry_column'];
                $geolayer->shapefile_name = null;

                if (!$geolayer->save()) {
                    throw new \Exception("Errore nel salvataggio del layer " . json_encode($geolayer->getErrors()), 1);
                }

                $tr->commit();
            } catch (\Exception $e) {
                $tr->rollBack();
                throw $e;
            }

            return $this->redirect(['view', 'id' => $geolayer->id]);
        }

        return $this->render('add_from_table_name', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing GeoLayer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $tr = Yii::$app->db->beginTransaction();

        try {
            if (!empty($model->shapefile_name)) {
                Yii::$app->db->createCommand()->dropTable(Yii::$app->params['geo_layer'].'.'.$model->table_name)->execute();
                $file_path = Yii::getAlias('@backend/uploads');
                if (is_dir($file_path.'/shapefiles/'.$model->table_name)) {
                    $dirname = $file_path.'/shapefiles/'.$model->table_name;
                    array_map('unlink', glob("$dirname/*.*"));
                    rmdir($dirname);
                }
            }

            $model->delete();

            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the GeoLayer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GeoLayer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GeoLayer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



    private function getColumnTypeByField($field)
    {
        switch ($field['type']) {
            case Shapefile::DBF_TYPE_CHAR:
                return 'VARCHAR(' . $field['size'].') NULL';
                break;
            case Shapefile::DBF_TYPE_DATE:
                return 'DATE NULL';
                break;
            case Shapefile::DBF_TYPE_LOGICAL:
                return 'BOOLEAN NULL';
                break;
            case Shapefile::DBF_TYPE_MEMO:
                return 'TEXT NULL';
                break;
            case Shapefile::DBF_TYPE_NUMERIC:
                return 'INTEGER NULL';
                break;
            case Shapefile::DBF_TYPE_FLOAT:
                return 'NUMERIC NULL';
                break;

            default:
                return 'TEXT NULL';
                break;
        }
    }

    private function getGeometryType($shape)
    {
        $pr = '4326';//$shape->getPRJ() ?? '4326';
        switch ($shape->getShapeType()) {
            case Shapefile::SHAPE_TYPE_NULL:
                return null;
                break;
            case Shapefile::SHAPE_TYPE_POINT:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POLYLINE:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POLYGON:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_MULTIPOINT:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POINTZ:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POLYLINEZ:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POLYGONZ:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_MULTIPOINTZ:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POINTM:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POLYLINEM:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_POLYGONM:
                return 'geometry NULL';
                break;
            case Shapefile::SHAPE_TYPE_MULTIPOINTM:
                return 'geometry NULL';
                break;
        }
    }

    private function getGeometryTypePlain($shape)
    {
        $pr = '4326';//$shape->getPRJ() ?? '4326';
        switch ($shape->getShapeType()) {
            case Shapefile::SHAPE_TYPE_NULL:
                return null;
                break;
            case Shapefile::SHAPE_TYPE_POINT:
                return 'POINT';
                break;
            case Shapefile::SHAPE_TYPE_POLYLINE:
                return 'POLYLINE';
                break;
            case Shapefile::SHAPE_TYPE_POLYGON:
                return 'POLYGON';
                break;
            case Shapefile::SHAPE_TYPE_MULTIPOINT:
                return 'MULTIPOINT';
                break;
            case Shapefile::SHAPE_TYPE_POINTZ:
                return 'POINTZ';
                break;
            case Shapefile::SHAPE_TYPE_POLYLINEZ:
                return 'POLYLINEZ';
                break;
            case Shapefile::SHAPE_TYPE_POLYGONZ:
                return 'POLYGONZ';
                break;
            case Shapefile::SHAPE_TYPE_MULTIPOINTZ:
                return 'MULTIPOINTZ';
                break;
            case Shapefile::SHAPE_TYPE_POINTM:
                return 'POINTM';
                break;
            case Shapefile::SHAPE_TYPE_POLYLINEM:
                return 'POLYLINEM';
                break;
            case Shapefile::SHAPE_TYPE_POLYGONM:
                return 'POLYGONM';
                break;
            case Shapefile::SHAPE_TYPE_MULTIPOINTM:
                return 'MULTIPOINTM';
                break;
        }
    }



    public function actionIndexQuery()
    {
        $searchModel = new GeoQuerySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_query', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateQuery()
    {
        $model = new GeoQuery();

        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \Exception("Errore nel salvataggio del layer " . json_encode($model->getErrors()), 1);
                }


                $tr->commit();
            } catch (\Exception $e) {
                $tr->rollBack();
                throw $e;
            }
            return $this->redirect(['index-query']);
        }

        return $this->render('create_query', [
            'model' => $model,
        ]);
    }

    public function actionUpdateQuery($id)
    {
        $model = $this->findQueryModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \Exception("Errore nel salvataggio del layer " . json_encode($model->getErrors()), 1);
                }


                $tr->commit();
            } catch (\Exception $e) {
                $tr->rollBack();
                throw $e;
            }
            return $this->redirect(['index-query']);
        }

        return $this->render('update_query', [
            'model' => $model,
        ]);
    }

    public function actionDeleteQuery($id)
    {
        $model = $this->findQueryModel($id);

        $tr = Yii::$app->db->beginTransaction();

        try {
            $model->delete();

            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            throw $e;
        }

        return $this->redirect(['index-query']);
    }


    protected function findQueryModel($id)
    {
        if (($model = GeoQuery::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionListLayerFields()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $data = Yii::$app->request->post('depdrop_parents');
        $id = isset($data[0]) ? $data[0] : "";

        $model = GeoLayer::findOne(['layer_name'=>$id]);
        if (!$model) {
            return [];
        }

        $fields = [];
        foreach ($model->fields as $key => $value) {
            if ($key != $model->geometry_column) {
                $fields[] = ['id'=>$key,'name'=>$key];
            }
        }

        return ['output'=>$fields,'selected'=>''];
    }
}
