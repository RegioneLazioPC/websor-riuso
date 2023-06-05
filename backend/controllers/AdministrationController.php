<?php

namespace backend\controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use common\models\UtlAutomezzo;
use common\models\VolSede;
use common\models\rbac\AuthItem;
use kartik\mpdf\Pdf;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Gestione ruoli amministrativi
 */
class AdministrationController extends Controller
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
                    
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user) {
                        Yii::error("Tentativo di accesso non autorizzato automezzo user: ".Yii::$app->user->getId());
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['roles', 'download-map', 'download-format','set-administrative'],
                        'permissions' => ['Admin']
                    ]
                ]
            ]
        ];
    }

    public function actionRoles()
    {
        $searchModel = new AuthItem();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);


        return $this->render('roles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSetAdministrative($name)
    {
        $trans = Yii::$app->db->beginTransaction();

        try {
            $item = AuthItem::findOne($name);
            if (!$item) {
                ResponseError::returnSingleError(404, "Ruolo non trovato");
            }

            $item->administrative = ($item->administrative == 1) ? 0 : 1;
            if (!$item->save()) {
                ResponseError::returnMultipleErrors($item->getErrors());
            }

            $trans->commit();

            return $this->redirect('roles');
        } catch (\Exception $e) {
            $trans->rollBack();
            throw $e;
        }
    }

    public function actionDownloadMap()
    {
        $permissions = AuthItem::find()
            ->orderBy(['name'=>SORT_ASC])
            ->where(['type'=>2])
            ->all();
        $roles = AuthItem::find()
            ->orderBy(['name'=>SORT_ASC])
            ->where(['type'=>1])
            ->all();
        $attributes = [
            'permission_name'=>'Permesso',
            'permission_description'=>'Descrizione'
        ];

        foreach ($roles as $role) {
            $attributes[$role->name] = $role->name;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $excel_row_index = 1;
        $sheet->fromArray([array_values($attributes)], null, 'A'.$excel_row_index);

        foreach ($permissions as $permission) {
            $excel_row_index++;

            $record = [$permission->name, $permission->description];

            $rls = $permission->getRoles()->indexBy('name')->all();
            foreach ($roles as $role) {
                if (isset($rls[$role->name])) {
                    $record[] = "X";
                } else {
                    $record[] = "";
                }
            }

            $sheet->fromArray([$record], null, 'A'.$excel_row_index);
        }
        

        $filename = 'Export_lista_permessi.xlsx';
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: GET');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '');
        header('Content-Transfer-Encoding: binary');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output', 'xls');
        exit;
    }


    public function actionDownloadFormat($format, $time)
    {
        $now = new \DateTime();
        $interval = new \DateInterval('P'.$time.'M');
        
        $start = $now->sub($interval);

        $records = Yii::$app->db->createCommand("SELECT * FROM
            vw_access_log
            WHERE datetime >= :startdate
            ORDER BY datetime DESC
            ", [':startdate'=>$start->format('Y-m-d H:i:s')])->queryAll();

        $title = 'Accessi ultimi ' . $time . ' mesi dal ' . date('d/m/Y');
        switch ($format) {
            case 'pdf':
                $filename = $title;
                $content = $this->renderPartial('access.php', [
                    'value' => $records,
                    'nome_file' => $filename
                ]);

                // setup kartik\mpdf\Pdf component
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    'destination' => Pdf::DEST_BROWSER,
                    'filename' => $filename,
                    'content' => $content,
                    'cssInline' => '.kv-heading-1{font-size:18px}',
                    'options' => ['title' => 'Accessi'],
                    'methods' => [
                    ]
                ]);
                Yii::$app->response->sendContentAsFile(
                    $pdf->render(),
                    'accessi.pdf',
                    ['inline'=>true]
                );
                break;
            
            default:
                $attributes = [
                    'username' => 'Username',
                    'datetime' => 'Data/ora',
                    'ip' => 'IP/Session index SPID',
                    'action' => 'Azione'
                ];

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $excel_row_index = 1;
                $sheet->fromArray([array_values($attributes)], null, 'A'.$excel_row_index);

                foreach ($records as $record) {
                    $excel_row_index++;

                    $sheet->fromArray([
                        $record['username'],
                        $record['datetime'],
                        $record['ip'],
                        $record['action']
                    ], null, 'A'.$excel_row_index);
                }
                
                $filename = 'Export_lista_permessi.xlsx';
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Headers: Content-Type');
                header('Access-Control-Allow-Methods: GET');
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '');
                header('Content-Transfer-Encoding: binary');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output', 'xls');
                exit;

                break;
        }
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
