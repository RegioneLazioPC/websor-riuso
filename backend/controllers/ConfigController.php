<?php

namespace backend\controllers;

use Yii;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlCategoriaAutomezzoAttrezzaturaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\data\ArrayDataProvider;
use common\models\app\AppConfig;
use common\models\app\config\Keys;

use common\models\UplTipoMedia;
use common\models\UplMedia;
use yii\web\UploadedFile;

/**
 * Gestione parametri di configurazione
 */
class ConfigController extends Controller
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
                        Yii::error("Tentativo di accesso non autorizzato configurazione user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'permissions' => ['Admin']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'permissions' => ['Admin']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'permissions' => ['Admin']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {

        $records = AppConfig::find()->all();
        $mapped = [];
        foreach ($records as $record) {
            $mapped[$record->key] = $record;
        }

        $provider_records = [];
        foreach (Keys::getKeyList() as $key => $value) {
            $added_record = (isset($mapped[$key])) ? $mapped[$key] : ['value'=>null];

            $provider_records[] = [
                'id' => $key,
                'key' => $key,
                'label' => $value['label'],
                'editable' => $value['editable'],
                'description' => $value['description'],
                'value' => json_decode($added_record['value'], true)
            ];
        }

        $provider = new ArrayDataProvider([
            'allModels' => $provider_records,
            'sort' => false,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'provider' => $provider
        ]);
    }

    public function actionUpdate($key)
    {
        
        $model = Keys::getDynamicModel($key);

        if (Yii::$app->request->method == 'POST') {
            //var_dump(Yii::$app->request->post('DynamicModel')); die();
            $model->attributes = Yii::$app->request->post('DynamicModel');

            if (!$model->validate()) {
                return $this->render('update', [
                'model' => $model,
                'avaible_keys' => Keys::$app_keys[$key]['form_values'],
                'key' => $key
                ]);
            }

            $data_sent = Yii::$app->request->post();
            $data_to_add = [];

            $correct_keys = [];
            foreach (Keys::$app_keys[$key]['form_values'] as $element) {
                $correct_keys[$element['key']] = $element['type'];
            }

            $existing_model = AppConfig::findOne(['key'=>$key]);
            
            foreach ($data_sent['DynamicModel'] as $_key => $value) {
                if (isset($correct_keys[$_key])) {
                    if ($correct_keys[$_key] == 'file') {
                        // il file dobbiamo caricarlo
                        $attachedFile = UploadedFile::getInstance($model, $_key);

                        if ($attachedFile) {
                            $tipo = $this->getTipoFile();
                            $media = new UplMedia;
                            $media->uploadFile($attachedFile, $tipo->id, [
                                'image/jpeg','image/png','image/jpg','application/pdf',
                                'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/msword','application/octet-stream','text/csv', 'text/plain'
                            ], "File non valido");
                            $media->refresh();

                            $data_to_add[$_key] = json_encode(['ID_FILE' => $media->id]);
                        } else {
                            if ($existing_model) {
                                $val = json_decode($existing_model->value, true);
                                $data_to_add[$_key] = @$val[$_key];
                            }
                        }
                    } else {
                        $data_to_add[$_key] = $value;
                    }
                }
            }

            $json_value = json_encode($data_to_add);
            
            if (!$existing_model) {
                $existing_model = new AppConfig;
                $existing_model->key = $key;
                $existing_model->label = Keys::$app_keys[$key]['label'];
            }

            $existing_model->value = $json_value;
            if (!$existing_model->save()) {
                throw new \Exception("Errore salvataggio chiave applicazione " . json_encode($existing_model->getErrors()), 1);
            }

            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
            'avaible_keys' => Keys::$app_keys[$key]['form_values'],
            'key' => $key
        ]);
    }

    public function actionDelete($key)
    {
        $existing_model = AppConfig::findOne(['key'=>$key]);
        if ($existing_model) {
            $existing_model->delete();
        }

        return $this->redirect('index');
    }


    private function getTipoFile()
    {
        $tipo = UplTipoMedia::find()->where(['descrizione'=>'Allegato elemento configurazione'])->one();
        if (empty($tipo)) {
            $tipo = new UplTipoMedia;
            $tipo->descrizione = 'Allegato elemento configurazione';
            $tipo->save();
        }

        return $tipo;
    }
}
