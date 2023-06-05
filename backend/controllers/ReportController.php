<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\UtlIngaggioSearch;
use yii\data\ArrayDataProvider;

use common\models\reportistica\FilterModel;
use common\models\reportistica\ViewReportAttivazioni;
use common\models\reportistica\ViewReportAttivazioniVolontari;
use common\models\reportistica\ViewReportInterventiElicotteri;

use common\models\RichiestaCanadair;
use common\models\RichiestaDos;
use common\models\RichiestaElicottero;
use common\models\UtlIngaggio;
use common\models\UtlEvento;

use kartik\mpdf\Pdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * ReportController
 */
class ReportController extends Controller
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
                        Yii::error(json_encode(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId())));
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'attivazioni', 'attivazioni-volontari',
                            'eventi','interventi','interventi-odv','interventi-tipologia',
                            'interventi-rifiutati','mezzi','elicotteri-per-intervento',
                            'coau','dettaglio-voli', 'report-pc-volontari',
                            'interventi-elicotteri', 'monitoraggio-eventi'
                        ],
                        'permissions' => ['exportData']
                    ]
                ],

            ],
        ];
    }



    private function buildFilters($params_map, $model = null)
    {

        $filter_model = ($model) ? $model : new FilterModel;
        
        $filter_model->load(Yii::$app->request->get());
        $filter_string = '';
        $filter_params = [];
        
        if ($filter_model->validate()) {
            foreach ($filter_model->getAttributes() as $param => $value) {
                if (isset($params_map[$param]) && !empty($filter_model->$param)) {
                    $filter_string .= ' AND ' . $params_map[$param];
                    $filter_params[':' . $param] = $value;
                }
            }
        }

        
        return [
            'filter_model' => $filter_model,
            'filter_string' => $filter_string,
            'filter_params' => $filter_params
        ];
    }


    /**
     * Report attivazioni
     * @return [type] [description]
     */
    public function actionAttivazioni()
    {
        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $ingaggiSearchModel = new ViewReportAttivazioni();
            $datas = $ingaggiSearchModel->search(Yii::$app->request->queryParams);
            
            $content = $this->renderPartial('pdf/attivazioni.php', [
                'data' => $datas->query->all()
            ]);
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report attivazioni'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $ingaggiSearchModel = new ViewReportAttivazioni();
            $ingaggiDataProvider = $ingaggiSearchModel->search(Yii::$app->request->queryParams);

            return $this->render('attivazioni', [
                'ingaggiSearchModel' => $ingaggiSearchModel,
                'ingaggiDataProvider' => $ingaggiDataProvider
            ]);
        }
    }

    /**
     * Attivazioni volontari
     * @return [type] [description]
     */
    public function actionAttivazioniVolontari()
    {
        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $ingaggiSearchModel = new ViewReportAttivazioniVolontari();
            $datas = $ingaggiSearchModel->search(Yii::$app->request->queryParams);
            
            $content = $this->renderPartial('pdf/attivazioni-volontari.php', [
                'data' => $datas->query->all()
            ]);
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report attivazioni'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $ingaggiSearchModel = new ViewReportAttivazioniVolontari();
            $ingaggiDataProvider = $ingaggiSearchModel->search(Yii::$app->request->queryParams);

            return $this->render('attivazioni-volontari', [
                'ingaggiSearchModel' => $ingaggiSearchModel,
                'ingaggiDataProvider' => $ingaggiDataProvider
            ]);
        }
    }

    /**
     * Report agenzia
     * @return [type] [description]
     */
    public function actionReportPcVolontari()
    {

        $connection = Yii::$app->getDb();

        $filters = $this->buildFilters([
            'date_from' => '(i.created_at::date >= :date_from::date)',
            'date_to' => '(i.created_at::date <= :date_to::date)'
        ]);

        $q = "SELECT 
                ana.id,
                v.ref_id as numero_regionale,
                v.denominazione,
                ana.cognome,
                ana.nome,
                ana.codfiscale,
                ARRAY_TO_STRING( ARRAY_AGG(distinct c.comune), ', ', '*') as comune,
                ARRAY_TO_STRING( ARRAY_AGG(distinct t.tipologia), ', ', '*') as tipologie,
                ARRAY_TO_STRING( ARRAY_AGG(i.created_at::date), ',', '*') as giorni
               FROM utl_ingaggio i
                 LEFT JOIN con_volontario_ingaggio cv ON cv.id_ingaggio = i.id
                 LEFT JOIN vol_volontario vol ON vol.id = cv.id_volontario
                 LEFT JOIN utl_anagrafica ana ON ana.id = vol.id_anagrafica
                 LEFT JOIN utl_evento e ON e.id = i.idevento
                 LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
                 LEFT JOIN loc_comune c ON c.id = e.idcomune
                 LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
              WHERE i.idevento IS NOT NULL 
              AND i.stato IN (1,3)
              ".$filters['filter_string']. "
              GROUP BY ana.id, v.id;";

        $command = $connection->createCommand($q);


        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }


        $result = $command->queryAll();

        // differenza di date
        $first_day = \DateTime::createFromFormat('Y-m-d', $_GET['FilterModel']['date_from']);
        $last_day = \DateTime::createFromFormat('Y-m-d', $_GET['FilterModel']['date_to']);

        
        $attributes = [
            'denominazione' => 'ODV',
            'cognome' => 'Cognome',
            'nome' => 'Nome',
            'codfiscale' => 'Codice fiscale',
            'comune' => 'Comuni',
            'tipologie' => 'Tipologia'
        ];
        $day_index_start = 5; // indice da cui iniziano le date

        $period = new \DatePeriod(
            $first_day,
            new \DateInterval('P1D'),
            $last_day
        );

        $date_indexes = [];
        foreach ($period as $value) {
            $attributes[ $value->format('Y-m-d') ] = $value->format('d/m/Y');
        }

        $attributes[ $last_day->format('Y-m-d') ] = $last_day->format('d/m/Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $excel_row_index = 1;
        $sheet->fromArray([$attributes], null, 'A'.$excel_row_index);

        foreach ($result as $record) {
            $excel_row_index++;

            $row_data = [];

            $date_volontario = explode(",", $record['giorni']);
            $current_i = 0;
            foreach ($attributes as $key => $value) {
                if ($current_i > $day_index_start) {
                    $row_data[$current_i] = (in_array($key, $date_volontario)) ? 'x' : '';
                } else {
                    $row_data[$current_i] = $record[$key];
                }

                $current_i++;
            }

            $sheet->fromArray([$row_data], null, 'A'.$excel_row_index);
        }

        $filename = 'Export_attivazioni_volontari_' . $first_day->format('Ymd') . "_" . $last_day->format('Ymd') . ".xlsx";
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: GET');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx";');
        header('Content-Transfer-Encoding: binary');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output', 'xls');
        exit;
    }


    public function actionEventi()
    {
        $connection = Yii::$app->getDb();
        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => '(e.dataora_evento >= :date_from OR e.closed_at >= :date_from)',
            'date_to' => '(e.dataora_evento <= :date_to OR e.closed_at <= :date_from)',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);


        $q = "WITH t as (SELECT 
                c.provincia_sigla,
                c.comune,  
                t.id as sottotipologia,
                pt.id as tipologia,
                count(e.id) as totale
                FROM loc_comune c
                LEFT JOIN utl_evento e ON e.idcomune = c.id AND e.idparent is null
                LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                WHERE c.id_regione = 12 AND e.idparent is null " . $filters['filter_string'] . "
                GROUP BY c.id, pt.id, t.id
                ORDER BY c.provincia_sigla, c.comune ASC)
            SELECT provincia_sigla as provincia, ''::TEXT as comune, sottotipologia, tipologia, sum(totale) FROM t 
            GROUP BY provincia, sottotipologia, tipologia
            UNION
            SELECT provincia_sigla as provincia, comune, sottotipologia, tipologia, sum(totale) FROM t 
            GROUP BY provincia_sigla, comune, sottotipologia, tipologia
            ORDER BY provincia ASC, comune ASC;";

        $command = $connection->createCommand($q);

        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $data = [];
        $tipologie = [];

        foreach ($result as $q_row) {
            if (!isset($data[$q_row['provincia']])) {
                $data[ $q_row['provincia'] ] = [
                'totale' => 0,
                'comuni' => [],
                'tipologie' => []
                ];
            }

            if (empty($q_row['comune'])) {
                // sto mettendo la provincia
                if (!isset($data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']])) {
                    $data[ $q_row['provincia'] ]['tipologie'][$q_row['tipologia']] = [
                        'sottotipologie' => [],
                        'totale' => 0
                    ];
                }

                if (!isset($data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']])) {
                    $data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']] = $q_row['sum'];
                }

                $data[ $q_row['provincia']]['totale'] += $q_row['sum'];
                $data[ $q_row['provincia']]['tipologie'][$q_row['tipologia']]['totale'] += $q_row['sum'];
            } else {
                // sto mettendo il comune
                if (!isset($data[ $q_row['provincia']]['comuni'][$q_row['comune']])) {
                    $data[ $q_row['provincia']]['comuni'][$q_row['comune']] = [
                        'tipologie' => [],
                        'totale' => 0
                    ];
                }

                if (!isset($data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']])) {
                    $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']] = [
                        'sottotipologie' => [],
                        'totale' => 0
                    ];
                }

                if (!isset($data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']])) {
                    $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']]['sottotipologie'][$q_row['sottotipologia']] = $q_row['sum'];
                }

                $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['tipologie'][$q_row['tipologia']]['totale'] += $q_row['sum'];

                $data[ $q_row['provincia']]['comuni'][$q_row['comune']]['totale'] += $q_row['sum'];
            }
        }

        $provider = [];

        foreach ($data as $provincia => $value) {
            $_provincia = [
                'provincia' => $provincia,
                'comune' => '',
                'totale' => $value['totale']
            ];

            foreach ($value['tipologie'] as $tipologia => $_value) {
                $_provincia['totale_'.$tipologia] = $_value['totale'];
                foreach ($_value['sottotipologie'] as $sottotipologia => $__value) {
                    $_provincia['totale_'.$sottotipologia] = $__value;
                }
            }

            $provider[] = $_provincia;

            foreach ($value['comuni'] as $comune => $_value) {
                $comune = [
                    'provincia' => $provincia,
                    'comune' => $comune,
                    'totale' => $_value['totale']
                ];

                foreach ($_value['tipologie'] as $tipologia => $__value) {
                    $comune['totale_'.$tipologia] = $__value['totale'];
                    foreach ($__value['sottotipologie'] as $sottotipologia => $___value) {
                        $comune['totale_'.$sottotipologia] = $___value;
                    }
                }

                $provider[] = $comune;
            }
        }

        $datProvider = new ArrayDataProvider([
            'allModels' => $provider,
            'pagination' => false
        ]);

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/eventi.php', [
                'data' => $provider,
                'tipologie' => $tipologie,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report eventi'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            return $this->render('eventi', [
                'dataProvider' => $datProvider,
                'tipologie' => $tipologie,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }



    public function actionInterventi()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => 'e.dataora_evento >= :date_from',
            'date_to' => 'e.dataora_evento <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'stato_ingaggio' => 'i.stato = :stato_ingaggio',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        $q = "WITH t as (
                SELECT 
                    c.provincia_sigla,
                    c.comune,
                    count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale_comune,
                    count( i.id ) filter (where i.stato = 3) as chiuso,
                    count( i.id ) filter (where i.stato = 0) as in_attesa,
                    count( i.id ) filter (where i.stato = 1) as confermato,
                    count( i.id ) filter (where i.stato = 2) as rifiutato,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
                    count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
                    FROM loc_comune c 
                    LEFT JOIN utl_evento e ON e.idcomune = c.id 
                    LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                    LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                    LEFT JOIN utl_ingaggio i ON i.idevento = e.id
                    WHERE c.id_regione = 12 " . $filters['filter_string'] . "
                    GROUP BY c.provincia_sigla, c.id )
            SELECT  provincia_sigla, ''::text as comune, SUM( t.totale_comune ) as totale_provincia, 0 as totale_comune,
                SUM( chiuso ) as chiuso,
                SUM( rifiutato ) as rifiutato,
                SUM( in_attesa ) as in_attesa,
                SUM( confermato ) as confermato,
                SUM( fuori_orario ) as fuori_orario,
                SUM( non_risponde ) as non_risponde,
                SUM( mezzo_non_disponibile ) as mezzo_non_disponibile,
                SUM( squadra_non_disponibile ) as squadra_non_disponibile,
                SUM( impegnata ) as impegnata,
                SUM( altro ) as altro
              FROM  t
              GROUP BY provincia_sigla
              UNION 
              SELECT  provincia_sigla, comune, 0 as totale_provincia, SUM( t.totale_comune ) as totale_comune,
                SUM( chiuso ) as chiuso,
                SUM( rifiutato ) as rifiutato,
                SUM( in_attesa ) as in_attesa,
                SUM( confermato ) as confermato,
                SUM( fuori_orario ) as fuori_orario,
                SUM( non_risponde ) as non_risponde,
                SUM( mezzo_non_disponibile ) as mezzo_non_disponibile,
                SUM( squadra_non_disponibile ) as squadra_non_disponibile,
                SUM( impegnata ) as impegnata,
                SUM( altro ) as altro  
             FROM t
              GROUP BY provincia_sigla, comune
              ORDER BY provincia_sigla, comune ASC";

        $command = $connection->createCommand($q);

        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/interventi.php', [
                'data' => $data,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report interventi'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false
            ]);

            return $this->render('interventi', [
                'dataProvider' => $datProvider,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }

    public function actionInterventiOdv()
    {


        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => 'e.dataora_evento >= :date_from',
            'date_to' => 'e.dataora_evento <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'stato_ingaggio' => 'i.stato = :stato_ingaggio',
            'odv' => 'v.ref_id = :odv',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        $q = "
        SELECT 
        v.ref_id as num_elenco_territoriale,
        v.denominazione,
        v.id as id_organizzazione,
        count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale,
        count( i.id ) filter (where i.stato = 3) as chiuso,
        count( i.id ) filter (where i.stato = 0) as in_attesa,
        count( i.id ) filter (where i.stato = 1) as confermato,
        count( i.id ) filter (where i.stato = 2) as rifiutato,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
        count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
        FROM utl_ingaggio i 
        LEFT JOIN utl_evento e ON e.id = i.idevento
        LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
        LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
        LEFT JOIN loc_comune c ON c.id = e.idcomune
        LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
        WHERE v.id is not null " . $filters['filter_string'] . "
        GROUP BY v.id
        ORDER BY v.ref_id ASC";

        $command = $connection->createCommand($q);
        //echo $command->getRawSql(); die();
        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/interventi-odv.php', [
                'data' => $data,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report interventi odv'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false
            ]);

            return $this->render('interventi-odv', [
                'dataProvider' => $datProvider,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }

    public function actionInterventiRifiutati()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from i.created_at) = :year',
            'month' => 'extract( \'month\' from i.created_at) = :month',
            'date_from' => 'i.created_at >= :date_from',
            'date_to' => 'i.created_at <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'odv' => 'v.ref_id = :odv'
        ]);

        $connection = Yii::$app->getDb();

        $q = "
        SELECT 
        v.ref_id as num_elenco_territoriale,
        v.denominazione,
        v.id as id_organizzazione,
        i.created_at,
        i.closed_at,
        a.targa,
        t.tipologia,
        st.tipologia as sottotipologia,
        e.num_protocollo,
        i.motivazione_rifiuto,
        i.note
        FROM utl_ingaggio i 
        LEFT JOIN utl_evento e ON e.id = i.idevento
        LEFT JOIN loc_comune c ON c.id = e.idcomune
        LEFT JOIN utl_evento f ON f.idparent = e.id /* f = fronte */
        LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
        LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
        LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
        LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
        WHERE v.id is not null AND i.stato = 2 " . $filters['filter_string'] . "
        ORDER BY v.ref_id ASC";

        $command = $connection->createCommand($q);
        //echo $command->getRawSql(); die();
        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/interventi-rifiutati.php', [
                'data' => $data,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report interventi rifiutati'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false
            ]);

            return $this->render('interventi-rifiutati', [
                'dataProvider' => $datProvider,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }

    public function actionInterventiTipologia()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from e.dataora_evento) = :year',
            'month' => 'extract( \'month\' from e.dataora_evento) = :month',
            'date_from' => 'e.dataora_evento >= :date_from',
            'date_to' => 'e.dataora_evento <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'stato_ingaggio' => 'i.stato = :stato_ingaggio',
            'tipologia' => 't.id = :tipologia',
            'sottotipologia' => 'st.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        $q = "WITH all_record as (SELECT 
            t.tipologia as tipologia,
            st.tipologia as sottotipologia,
            count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale,
            count( i.id ) filter (where i.stato = 3) as chiuso,
            count( i.id ) filter (where i.stato = 0) as in_attesa,
            count( i.id ) filter (where i.stato = 1) as confermato,
            count( i.id ) filter (where i.stato = 2) as rifiutato,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
            FROM utl_ingaggio i 
            LEFT JOIN utl_evento e ON e.id = i.idevento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
            WHERE t.id is not null " . $filters['filter_string'] . " 
            GROUP BY t.id, st.id
            UNION ALL 
            SELECT 
            t.tipologia as tipologia,
            ' - ' as sottotipologia,
            count( i.id ) filter (WHERE e.stato = 'Chiuso') as totale,
            count( i.id ) filter (where i.stato = 3) as chiuso,
            count( i.id ) filter (where i.stato = 0) as in_attesa,
            count( i.id ) filter (where i.stato = 1) as confermato,
            count( i.id ) filter (where i.stato = 2) as rifiutato,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 1) as fuori_orario,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 2) as non_risponde,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 3) as mezzo_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 4) as squadra_non_disponibile,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 6) as impegnata,
            count( i.id ) filter (where i.stato = 2 and i.motivazione_rifiuto = 5) as altro
            FROM utl_ingaggio i 
            LEFT JOIN utl_evento e ON e.id = i.idevento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN vol_organizzazione v ON v.id = i.idorganizzazione
            WHERE t.id is not null " . $filters['filter_string'] . "
            GROUP BY t.id) 
            SELECT * FROM all_record ORDER BY tipologia ASC, sottotipologia ASC";

        $command = $connection->createCommand($q);

        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $data = $command->queryAll();

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/interventi-tipologia.php', [
                'data' => $data,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report interventi per tipologia'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false
            ]);

            return $this->render('interventi-tipologia', [
                'dataProvider' => $datProvider,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }

  
    public function actionMezzi()
    {

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from i.created_at) = :year',
            'month' => 'extract( \'month\' from i.created_at) = :month',
            'day' => 'extract( \'day\' from i.created_at) = :day',
            'date_from' => 'i.created_at >= :date_from',
            'date_to' => 'i.created_at <= :date_to',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune',
            'tipo_mezzo' => 'tipo.id = :tipo_mezzo',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
        ]);

        $connection = Yii::$app->getDb();

        $q = "WITH t as (SELECT 
                        extract( 'year' from i.created_at) as anno,
                        extract( 'month' from i.created_at) as mese,
                        extract( 'day' from i.created_at) as giorno,
                        STRING_AGG( DISTINCT( agg.descrizione), ', ' ) as tipologia,
                        count(DISTINCT(i.id)) as total
                        FROM utl_ingaggio i 
                        LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
                        LEFT JOIN utl_automezzo_tipo tipo ON tipo.id = a.idtipo
                        LEFT JOIN con_aggregatore_tipologie_tipologie con ON con.id_tipo_automezzo = tipo.id 
                        LEFT JOIN utl_aggregatore_tipologie agg ON agg.id = con.id_aggregatore
                        LEFT JOIN utl_evento e ON e.id = i.idevento
                        LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                        LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                        LEFT JOIN loc_comune c ON c.id = e.idcomune
                        WHERE agg.descrizione is not null AND i.stato != 3 " . $filters['filter_string'] . "
                        GROUP BY i.created_at
                        ORDER BY anno DESC, mese DESC)
             SELECT anno, mese::INTEGER, giorno::INTEGER, tipologia, sum(total) FROM t 
             GROUP BY anno, mese, giorno, tipologia
             UNION
             SELECT anno, mese::INTEGER, null as giorno, tipologia, sum(total) FROM t 
             GROUP BY anno, mese, tipologia
             UNION
             SELECT anno, null as mese, null as giorno, tipologia, sum(total) FROM t 
             GROUP BY anno, tipologia
             ORDER BY anno DESC, mese DESC, giorno DESC;";

        $command = $connection->createCommand($q);
        
        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $data = [];
        $provider = [];
        $tipologie = [];
        

        foreach ($result as $q_row) {
            if (!in_array($q_row['tipologia'], $tipologie)) {
                $tipologie[] = $q_row['tipologia'];
            }

            if (!isset($data[$q_row['anno']])) {
                $data[$q_row['anno']] = [
                'totale' => 0,
                'mesi' => []
                ];
            }
            // vediamo se sta splittando un anno o un mese
            if (empty($q_row['mese'])) {
                // è anno
                if (!isset($data[$q_row['anno']][$q_row['tipologia']])) {
                    $data[$q_row['anno']][$q_row['tipologia']] = $q_row['sum'];
                }
                $data[$q_row['anno']]['totale'] += intval($q_row['sum']);
            } else {
                if (!isset($data[$q_row['anno']]['mesi'][$q_row['mese']])) {
                    $data[$q_row['anno']]['mesi'][$q_row['mese']] = [
                        'totale'=>0,
                        'giorni' => []
                    ];
                }
                
                if (empty($q_row['giorno'])) {
                    // è mese
                    if (!isset($data[$q_row['anno']]['mesi'][$q_row['mese']][$q_row['tipologia']])) {
                        $data[$q_row['anno']]['mesi'][$q_row['mese']][$q_row['tipologia']] = $q_row['sum'];
                    }

                    $data[$q_row['anno']]['mesi'][$q_row['mese']]['totale'] += intval($q_row['sum']);
                } else {
                    // è giorno
                    if (!isset($data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']])) {
                        $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']] = [
                            'totale' => 0
                        ];
                    }

                    if (!isset($data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']][$q_row['tipologia']])) {
                        $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']][$q_row['tipologia']] = $q_row['sum'];
                    }

                    $data[$q_row['anno']]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['totale'] += intval($q_row['sum']);
                }
            }
        }


        foreach ($data as $anno => $righe) {
            $el = [
                'anno' => $anno,
                'mese' => null
            ];
            foreach ($righe as $key => $value) {
                if ($key != 'mesi') {
                    $el[$key] = $value;
                }
            }

            $provider[] = $el;

            foreach ($righe['mesi'] as $mese => $mese_righe) {
                $el = [
                    'anno' => $anno,
                    'mese' => $mese
                ];
                foreach ($mese_righe as $key => $value) {
                    $el[$key] = $value;
                }

                $provider[] = $el;


                foreach ($mese_righe['giorni'] as $giorno => $giorno_righe) {
                    $el = [
                        'anno' => $anno,
                        'mese' => $mese,
                        'giorno' => $giorno
                    ];

                    foreach ($giorno_righe as $key => $value) {
                        $el[$key] = $value;
                    }

                    $provider[] = $el;
                }
            }
        }

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/mezzi.php', [
                'data' => $provider,
                'tipologie' => $tipologie,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report mezzi'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $provider,
                'pagination' => false
            ]);

            return $this->render('mezzi', [
                'dataProvider' => $datProvider,
                'tipologie' => $tipologie,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }


    public function actionElicotteriPerIntervento()
    {
        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from i.created_at) = :year',
            'month' => 'extract( \'month\' from i.created_at) = :month',
            'date_from' => 'i.created_at >= :date_from',
            'date_to' => 'i.created_at <= :date_to',
            'tipologia' => 'pt.id = :tipologia',
            'sottotipologia' => 't.id = :sottotipologia',
            'pr' => 'c.provincia_sigla = :pr',
            'comune' => 'c.id = :comune'
        ]);

        $connection = Yii::$app->getDb();


        $q = "WITH t as (SELECT 
                extract( 'year' from i.created_at) as anno,
                extract( 'month' from i.created_at) as mese,
                extract( 'day' from i.created_at) as giorno,
                c.provincia_sigla,
                c.comune, 
                count(DISTINCT(i.id)) as total
                FROM utl_ingaggio i 
                LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
                LEFT JOIN utl_automezzo_tipo tipo ON tipo.id = a.idtipo
                LEFT JOIN utl_evento e ON e.id = i.idevento
                LEFT JOIN utl_tipologia t ON t.id = e.sottotipologia_evento
                LEFT JOIN utl_tipologia pt ON pt.id = t.idparent
                LEFT JOIN loc_comune c ON c.id = e.idcomune
                WHERE tipo.is_mezzo_aereo = true AND tipo.descrizione ilike 'Elicottero' " . $filters['filter_string'] . "
                GROUP BY anno, mese, giorno, t.id, c.comune, c.provincia_sigla
                ORDER BY anno DESC, mese DESC, c.provincia_sigla DESC, c.comune DESC)
            SELECT anno, mese::INTEGER, giorno::INTEGER, provincia_sigla as provincia, comune, sum(total) FROM t 
            GROUP BY anno, mese, giorno, provincia_sigla, comune
            UNION
            SELECT anno, mese::INTEGER, giorno::INTEGER, provincia_sigla as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese, giorno, provincia_sigla
            UNION
            SELECT anno, mese::INTEGER, giorno::INTEGER, ''::TEXT as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese, giorno
            UNION
            SELECT anno, mese::INTEGER, null as giorno, provincia_sigla as provincia, comune, sum(total) FROM t 
            GROUP BY anno, mese, provincia_sigla, comune
            UNION
            SELECT anno, mese::INTEGER, null as giorno, provincia_sigla as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese, provincia_sigla
            UNION
            SELECT anno, mese::INTEGER, null as giorno, ''::TEXT as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno, mese
            UNION
            SELECT anno, null as mese, null as giorno, ''::TEXT as provincia, ''::TEXT as comune, sum(total) FROM t 
            GROUP BY anno
            ORDER BY anno DESC, mese DESC, giorno DESC, provincia ASC, comune ASC;";

        $command = $connection->createCommand($q);
        
        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $provider = [];
        $comuni = [];
        $data = [];
        $province = [];
        foreach ($result as $q_row) {
            if (!empty($q_row['anno']) && empty($q_row['mese'])) {
                $data[ $q_row['anno'] ] = [
                'total' => $q_row['sum'],
                'mesi' => []
                ];
            }

            if (!empty($q_row['mese']) && empty($q_row['giorno']) && empty($q_row['provincia'])) {
                $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ] = [
                    'total' => $q_row['sum'],
                    'giorni' => [],
                    'province' => []
                ];
            }

            if (!empty($q_row['giorno']) && empty($q_row['provincia'])) {
                $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']] = [
                    'total' => $q_row['sum'],
                    'province' => []
                ];
            }

            if (!empty($q_row['provincia']) && empty($q_row['comune'])) {
                if (empty($q_row['giorno'])) {
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ][ 'total_provincia_'.$q_row['provincia'] ] = $q_row['sum'];
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['province'][ $q_row['provincia'] ] = [
                        'comuni' => [],
                        'total'=> $q_row['sum']
                    ];

                    // default
                    if (!isset($data[ $q_row['anno'] ]['total_provincia_'.$q_row['provincia']])) {
                        $data[ $q_row['anno'] ]['total_provincia_'.$q_row['provincia']] = 0;
                    }

                    $data[ $q_row['anno'] ]['total_provincia_'.$q_row['provincia']] += intval($q_row['sum']);

                    if (!isset($data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_provincia_'.$q_row['provincia']])) {
                        $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_provincia_'.$q_row['provincia']] = intval($q_row['sum']);
                    }
                    
                    if (!isset($province[$q_row['provincia']])) {
                        $province[$q_row['provincia']] = ['comuni'=>[]];
                    }
                } else {
                    // inserisco i dati del giorno
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']][ 'total_provincia_'.$q_row['provincia'] ] = $q_row['sum'];

                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']]['province'][ $q_row['provincia'] ] = [
                        'comuni' => [],
                        'total'=> $q_row['sum']
                    ];
                }
            }

            if (!empty($q_row['comune'])) {
                if (empty($q_row['giorno'])) {
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['province'][ $q_row['provincia'] ]['comuni'][ $q_row['comune'] ] = $q_row['sum'];

                    if (!isset($data[ $q_row['anno'] ]['total_comune_' . self::normalize($q_row['comune']) ])) {
                        $data[ $q_row['anno'] ]['total_comune_' . self::normalize($q_row['comune']) ] = 0;
                    }

                    if (!isset($data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_comune_' . self::normalize($q_row['comune']) ])) {
                        $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_comune_' . self::normalize($q_row['comune']) ] = 0;
                    }
                    
                    $data[ $q_row['anno'] ]['total_comune_' . self::normalize($q_row['comune']) ] += intval($q_row['sum']);
                    $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['total_comune_' . self::normalize($q_row['comune']) ] += intval($q_row['sum']);
                } else {
                    $data[ $q_row['anno'] ]['mesi'][ $q_row['mese'] ]['giorni'][$q_row['giorno']]['province'][ $q_row['provincia'] ]['comuni'][ $q_row['comune'] ] = $q_row['sum'];

                    if (!isset($data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['total_comune_' . self::normalize($q_row['comune']) ])) {
                        $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['total_comune_' . self::normalize($q_row['comune']) ] = 0;
                    }

                    $data[ $q_row['anno'] ]['mesi'][$q_row['mese']]['giorni'][$q_row['giorno']]['total_comune_' . self::normalize($q_row['comune']) ] += intval($q_row['sum']);
                }

                if (!in_array($q_row['comune'], $province[$q_row['provincia']]['comuni'])) {
                    $province[$q_row['provincia']]['comuni'][] = $q_row['comune'];
                }
            }
        }

        foreach ($data as $anno => $dati) {
            $year = [
                'anno' => $anno,
                'total' => $dati['total']
            ];

            $month_row = [];
            $day_row = [];
            
            foreach ($dati['mesi'] as $m_key => $m_value) {
                $month = [
                    'anno' => $anno,
                    'mese' => $m_key,
                    'total' => $m_value['total']
                ];

                

                foreach ($province as $key => $value) {
                    foreach ($value['comuni'] as $_key) {
                        $year[ 'total_comune_' . self::normalize($_key) ] = isset($dati['total_comune_' . self::normalize($_key)]) ? $dati['total_comune_' . self::normalize($_key)] : 0;

                        $month[ 'total_comune_' . self::normalize($_key) ] = isset($dati['mesi'][$m_key]['total_comune_' . self::normalize($_key)]) ? $dati['mesi'][$m_key]['total_comune_' . self::normalize($_key)] : 0;
                    }

                    $year[ 'total_provincia_' . self::normalize($key) ] = isset($dati['total_provincia_' . self::normalize($key)]) ? $dati['total_provincia_' . self::normalize($key)] : 0;

                    $month[ 'total_provincia_' . self::normalize($key) ] = isset($dati['mesi'][$m_key]['total_provincia_' . self::normalize($key)]) ? $dati['mesi'][$m_key]['total_provincia_' . self::normalize($key)] : 0;
                }

                $month_row[] = $month;

                // i giorni li indicizziamo per mese
                $day_row[$m_key] = [];
                
                foreach ($m_value['giorni'] as $giorno => $giorno_row) {
                    $day = [
                        'anno' => $anno,
                        'mese' => $m_key,
                        'giorno' => $giorno,
                        'total' => $giorno_row['total']
                    ];
                    foreach ($province as $key => $value) {
                        foreach ($value['comuni'] as $_key) {
                            $day[ 'total_comune_' . self::normalize($_key) ] = isset($dati['mesi'][$m_key]['giorni'][$giorno]['total_comune_' . self::normalize($_key)]) ? $dati['mesi'][$m_key]['giorni'][$giorno]['total_comune_' . self::normalize($_key)] : 0;
                        }
                        

                        $day[ 'total_provincia_' . self::normalize($key) ] = isset($dati['mesi'][$m_key]['giorni'][$giorno]['total_provincia_' . self::normalize($key)]) ? $dati['mesi'][$m_key]['giorni'][$giorno]['total_provincia_' . self::normalize($key)] : 0;
                    }
                    

                    $day_row[$m_key][] = $day;
                }
            }

            $provider[] = $year;
            foreach ($month_row as $month) {
                $provider[] = $month;
                if (isset($day_row[$month['mese']])) {
                    $provider = array_merge($provider, $day_row[$month['mese']]);
                }
            }
        }


        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/elicotteri.php', [
                'data' => $provider,
                'province' => $province,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report elicotteri'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $provider,
                'pagination' => false
            ]);

            return $this->render('elicotteri', [
                'dataProvider' => $datProvider,
                'province' => $province,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }

    /**
     * Normalizza il nome dei comuni, funzione banale
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public static function normalize($string)
    {
        return preg_replace("/[^a-zA-Z0-9-]/", "_", $string);
    }


    public function actionCoau()
    {
        $model = new FilterModel;
        if (empty(Yii::$app->request->get('FilterModel')) || empty(Yii::$app->request->get('FilterModel')['dataora'])) {
            $model->dataora = date('Y-m-d H:i');
        }

        $filters = $this->buildFilters([
            'dataora' => '
            (e.dataora_evento::DATE = :dataora::DATE OR 
            (e.dataora_evento::DATE <= :dataora::DATE AND e.closed_at::DATE >= :dataora::DATE) OR 
            (e.dataora_evento::DATE <= :dataora::DATE AND e.closed_at is null)
            )'/*
            (e.closed_at >= :dataora AND e.dataora_evento <= :dataora)'*/,
            'pr' => 'l.provincia_sigla = :pr'
        ], $model);

        $connection = Yii::$app->getDb();

        $q = "WITH t as (
            SELECT 
                /*extract( 'year' from e.dataora_evento) as anno,
                extract( 'month' from e.dataora_evento) as mese,*/
                l.provincia_sigla as provincia,
                count(DISTINCT(e.id)) as total,
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia ilike 'boschivo' ) as num_boschivo,
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia not ilike 'boschivo' ) as num_non_boschivo,
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia ilike 'boschivo' AND e.closed_at <= :dataora ) as num_boschivo_chiuso, 
                count( DISTINCT(e.id) ) filter ( where t.tipologia = 'Incendio' AND st.tipologia ilike 'boschivo' AND (e.closed_at > :dataora OR e.closed_at is null) ) as num_boschivo_aperto, 
                (count(DISTINCT(e.id)) filter (WHERE el.id is not null AND el.engaged = true and el.edited = 1 AND c.id is null)) as solo_regionali
            FROM utl_evento e
            LEFT JOIN richiesta_canadair c ON c.idevento = e.id AND c.engaged = true AND c.edited = 1
            LEFT JOIN richiesta_elicottero el ON el.idevento = e.id AND el.engaged = true AND el.edited = 1 AND el.deleted <> 1
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia st ON st.id = e.sottotipologia_evento
            LEFT JOIN loc_comune l ON l.id = e.idcomune
            WHERE l.id_regione = 12 AND e.idparent is null 
            " . $filters['filter_string'] . "
            GROUP BY /*anno, mese,*/ provincia)
            SELECT 
                provincia,/* anno, mese,*/
                total, num_boschivo, num_non_boschivo, num_boschivo_chiuso, num_boschivo_aperto, solo_regionali
            FROM t
            WHERE num_boschivo > 0 OR num_non_boschivo > 0 OR num_boschivo_chiuso > 0 OR num_boschivo_aperto > 0 OR solo_regionali > 0
            ORDER BY provincia ASC/*, anno DESC, mese DESC*/
            ;";

        $keys = [
                'num_boschivo',
                'solo_regionali',
                'num_boschivo_chiuso',
                'num_boschivo_aperto',
                'num_non_boschivo',
            ];

        $command = $connection->createCommand($q);
        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }

        $result = $command->queryAll();
        $indices = [];
        $data = [];
        $last_row = [
            'regione' => 'Lazio',
            'children' => [],
            'provincia' => 'Totale',
            'num_boschivo' => 0,
            'solo_regionali' => 0,
            'num_boschivo_chiuso' => 0,
            'num_boschivo_aperto' => 0,
            'num_non_boschivo' => 0,
        ];
        // usiamo dei semplici indici di riferimento per prendere i figli successivamente
        $n = 0;
        foreach ($result as $row_q) {
            $result[$n]['regione'] = 'Lazio';
            foreach ($keys as $k) {
                $last_row[$k] += intval($row_q[$k]);
            }
            $n++;
        }

        $result[] = $last_row;

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/coau.php', [
                'data' => $result,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report coau'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $result,//$data,
                'pagination' => false
            ]);

            return $this->render('coau', [
                'dataProvider' => $datProvider,
                'filter_model' => $filters['filter_model']
            ]);
        }
    }


    public function actionDettaglioVoli()
    {
        $model = new FilterModel;
        if (empty(Yii::$app->request->get('FilterModel')) || empty(Yii::$app->request->get('FilterModel')['year'])) {
            $model->year = date('Y');
        }

        $filters = $this->buildFilters([
            'year' => 'extract( \'year\' from v.start_local_timestamp) = :year',
            'date_from' => 'v.start_local_timestamp >= :date_from',
            'date_to' => 'v.start_local_timestamp <= :date_to',
        ], $model);

        $connection = Yii::$app->getDb();

        $q = "WITH names as (SELECT distinct device_id, ARRAY_TO_STRING( ARRAY_AGG ( distinct device_name ), ', ', '') as device_name 
            FROM utl_arka_voli av
            GROUP BY device_id), t as (
            SELECT 
            device_id,
            extract( 'year' from v.start_local_timestamp) as anno,
            extract( 'month' from v.start_local_timestamp) as mese, 
            stop_local_timestamp,
            start_local_timestamp,
            ore_di_volo
            FROM utl_arka_voli v
            WHERE 1=1 " . $filters['filter_string'] . "
            )
            SELECT anno, null as mese, null::integer as giorno, t.device_id, names.device_name, sum( ((stop_local_timestamp - start_local_timestamp)) )
            FROM t
            LEFT JOIN names ON names.device_id = t.device_id
            GROUP BY anno, t.device_id, names.device_name
            UNION
            SELECT anno, mese, null::integer as giorno, t.device_id, names.device_name, sum( ((stop_local_timestamp - start_local_timestamp)) )
            FROM t
            LEFT JOIN names ON names.device_id = t.device_id
            GROUP BY anno, mese, t.device_id, names.device_name
            UNION
            SELECT anno, mese, extract('day' from start_local_timestamp)::integer as giorno, t.device_id, names.device_name, sum( ((stop_local_timestamp - start_local_timestamp)) )
            FROM t
            LEFT JOIN names ON names.device_id = t.device_id
            GROUP BY anno, mese, giorno, t.device_id, names.device_name
            ORDER BY anno DESC, mese DESC, giorno DESC;";

        $command = $connection->createCommand($q);

        if (!empty($filters['filter_params'])) {
            $command->bindValues($filters['filter_params']);
        }
        
        $result = $command->queryAll();

        $indices = [];
        $data = [];
        $parent_indexes = [];
        $elicotteri = [];
        $datas = [];
        $final = [];
        // usiamo dei semplici indici di riferimento per prendere i figli successivamente
        $k = 0;

        foreach ($result as $row_q) {
            $elicotteri[$row_q['device_id']] = [
                'device_id' => $row_q['device_id'],
                'device_name' => $row_q['device_name']
            ];
            // siamo nel gruppo del totale mensile per elicottero
            // imposto l'indice per poter mettere il figlio dopo
            if (empty($row_q['giorno']) && !empty($row_q['mese'])) {
                $parent_indexes[$row_q['device_id']] = $k;
            }

            if (!empty($row_q['giorno'])) {
                if (empty($result[ $parent_indexes[$row_q['device_id']] ]['children'])) {
                    $result[ $parent_indexes[$row_q['device_id']] ]['children'] = [];
                }
                $result[ $parent_indexes[$row_q['device_id']] ]['children'][] = $row_q;
                unset($result[$k]);
            }

            if (!isset($datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ])) {
                $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ] = [
                    'anno' => $row_q['anno'],
                    'mese' => $row_q['mese'],
                    'giorno' => $row_q['giorno'],
                    'device_' . $row_q['device_id'] => $row_q['sum']
                ];
            } else {
                $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ]['device_' . $row_q['device_id'] ] = $row_q['sum'];
            }

            if (empty($row_q['giorno'])) {
                $final[
                    $row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno']
                ] = $datas[ $row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ];
            } else {
                if (!isset(
                    $final[ $row_q['anno'] . "_" . $row_q['mese'] . "_" ]['children']
                )) {
                    $final[$row_q['anno'] . "_" . $row_q['mese'] . "_"]['children'] = [];
                }

                $final[ $row_q['anno'] . "_" . $row_q['mese'] . "_" ]['children'][] = $datas[$row_q['anno'] . "_" . $row_q['mese'] . "_" . $row_q['giorno'] ];
            }

            $k++;
        }

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $content = $this->renderPartial('pdf/dettaglio-voli.php', [
                'data' => $final,
                'elicotteri' => $elicotteri,
                'filter_model' => $filters['filter_model']
            ]);
            
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report dettaglio voli'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $datProvider = new ArrayDataProvider([
                'allModels' => $final,//$result,
                'pagination' => false
            ]);

            return $this->render('dettaglio-voli', [
                'dataProvider' => $datProvider,
                'filter_model' => $filters['filter_model'],
                'elicotteri' => $elicotteri
            ]);
        }
    }
    

    public function actionInterventiElicotteri()
    {

        if (Yii::$app->request->get('format') && Yii::$app->request->get('format') === 'pdf') {
            $ingaggiSearchModel = new ViewReportInterventiElicotteri();
            $datas = $ingaggiSearchModel->search(Yii::$app->request->queryParams);
            
            $content = $this->renderPartial('pdf/interventi-elicotteri.php', [
                'data' => $datas->query->all()
            ]);
            
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Report interventi elicotteri'],
                'methods' => [
                ]
            ]);

            Yii::$app->response->sendContentAsFile(
                $pdf->render(),
                'report.pdf',
                ['inline'=>true]
            );
        } else {
            $ingaggiSearchModel = new ViewReportInterventiElicotteri();
            $ingaggiDataProvider = $ingaggiSearchModel->search(Yii::$app->request->queryParams);

            return $this->render('interventi-elicotteri', [
                'ingaggiSearchModel' => $ingaggiSearchModel,
                'ingaggiDataProvider' => $ingaggiDataProvider
            ]);
        }
    }



    private function replaceDashAndBar($txt)
    {
        return str_replace("||||", ",", str_replace("###", " ", $txt));
    }

    private function getCapMessageDate($v)
    {
        if (!$v) {
            return '';
        }
        $dt = \DateTime::createFromFormat("Y-m-d H:i:sP", str_replace("T", " ", $v));
        if (is_bool($dt)) {
            return '';
        }

        return $dt->format('d/m/Y H:i:s');
    }

    private function checkCapMessageDate($v, $date_from, $date_to)
    {
        if (!$v) {
            return '';
        }
        $dt = \DateTime::createFromFormat("Y-m-d H:i:sP", str_replace("T", " ", $v));
        if (is_bool($dt)) {
            return false;
        }

        return $date_from <= $dt->format('Y-m-d H:i:s') && $date_to >= $date_to;
    }

    public function actionMonitoraggioEventi()
    {

        $filter_model = new FilterModel;

        if (Yii::$app->request->get('FilterModel')) {
            if (isset(Yii::$app->request->get('FilterModel')['date_from'])) {
                $dt = Yii::$app->request->get('FilterModel')['date_from'];
            } else {
                $dt = date('Y-m-d');
            }
        } else {
            $dt = date('Y-m-d');
        }

        $filter_model->date_from = $dt;

        $dt_to = $dt . " 23:59:59";
        $dt = $dt . " 00:00:00";

        $eventi = UtlEvento::find()
        ->andWhere(['<=','(dataora_evento)::date',$dt])
        ->andWhere(['>=', '(closed_at)::date', $dt_to])
        ->orderBy(['id'=>SORT_DESC])
        ->all();
        
        $id_eventi_correnti = array_map(function ($evento) {
            return $evento->id;
        }, $eventi);
        $richieste_elicottero = RichiestaElicottero::find()->where(['engaged'=>true])
            ->andWhere('id_elicottero is not null')
            ->andWhere(['idevento'=>$id_eventi_correnti])
            ->andWhere(['=','(created_at)::date',$dt])
            ->orderBy(['created_at'=>SORT_ASC])
            ->all();
        $richieste_canadair = RichiestaCanadair::find()->where(['engaged'=>true])
            ->andWhere('codice_canadair is not null')
            ->andWhere(['idevento'=>$id_eventi_correnti])
            ->andWhere(['=','(created_at)::date',$dt])
            ->orderBy(['created_at'=>SORT_ASC])
            ->all();
        $richieste_dos = RichiestaDos::find()->where(['engaged'=>true])
            ->andWhere('codicedos is not null')
            ->andWhere(['idevento'=>$id_eventi_correnti])
            ->andWhere(['=','(created_at)::date',$dt])
            ->orderBy(['created_at'=>SORT_ASC])
            ->all();

        $array_risultati = [];
        $codici_elicottero = [];
        $codici_canadair = [];
        $codici_dos = [];

        foreach ($richieste_elicottero as $richiesta) {
            $codici_elicottero[] = [
                'codice' => $richiesta->codice_elicottero,
                'id_evento' => $richiesta->idevento,
                'elicottero' => $richiesta->elicottero->targa
            ];
        }

        foreach ($richieste_canadair as $richiesta) {
            $codici_canadair[] = [
                'codice' => $richiesta->codice_canadair,
                'id_evento' => $richiesta->idevento
            ];
        }

        foreach ($richieste_dos as $richiesta) {
            $codici_dos[] = [
                'codice' => $richiesta->codicedos,
                'id_evento' => $richiesta->idevento
            ];
        }

        foreach ($eventi as $evento) {
            $elemento = [
                'id' => $evento->id,
                'comune' => $evento->comune->comune,
                'provincia' => $evento->comune->provincia->sigla,
                'indirizzo' => !empty($evento->luogo) ? $evento->luogo : $evento->indirizzo,
                'elicotteri' => [],
                'canadair' => [],
                'dos' => [],
                'attivazioni' => [],
                'veicoli_cap' => []
            ];

            foreach ($codici_elicottero as $value) {
                if ($value['id_evento'] == $evento->id) {
                    $elemento['elicotteri'][] = $value;
                }
            }

            foreach ($codici_canadair as $value) {
                if ($value['id_evento'] == $evento->id) {
                    $elemento['canadair'][] = $value;
                }
            }

            foreach ($codici_dos as $value) {
                if ($value['id_evento'] == $evento->id) {
                    $elemento['dos'][] = $value;
                }
            }

            $attivazioni = UtlIngaggio::find()
                ->where(['idevento'=>$evento->id])
                ->andWhere(['=','(created_at)::date',$dt])
                ->andWhere('motivazione_rifiuto is not null')
                ->all();

            foreach ($attivazioni as $attivazione) {
                $tipologia = '';
                $odv = '';
                $identificativo = '';
                if (empty($attivazione->organizzazione)) {
                    continue;
                } else {
                    $odv = $attivazione->organizzazione->ref_id;
                }
                if (!empty($attivazione->automezzo)) {
                    $tipologia = $attivazione->automezzo->tipo->descrizione;
                    $identificativo = $attivazione->automezzo->targa;
                }
                if (!empty($attivazione->attrezzatura)) {
                    $tipologia = $attivazione->attrezzatura->tipo->descrizione;
                    $identificativo = $attivazione->attrezzatura->modello;
                }



                $elemento['attivazioni'][] = [
                    'odv' => $odv,
                    'tipologia' => $tipologia,
                    'identificativo' => $identificativo
                ];
            }

            $vehicles = [];
            // veicoli da cap
            foreach ($evento->getLastCapMessages()->all() as $cap_message) {
                $json_data = $cap_message->json_content;
                $params = [];
                try {
                    $params = $json_data['info']['parameter'];
                } catch (\Exception $e) {
                }
                
                foreach ($params as $param) {
                    if ($param['valueName'] == 'VEHICLES') {
                        $txt = preg_replace_callback('~"[^"]*"~', function ($m) {
                            return preg_replace('~\s~', '###', $m[0]);
                        }, $param['value']);

                        $txt = preg_replace_callback('~"[^"]*"~', function ($m) {
                            return preg_replace('~\,~', '||||', $m[0]);
                        }, $txt);
                        
                        $v = explode(" ", $txt);
                        foreach ($v as $row) {
                            $row_data = explode(",", $row);
                            // se data partenza != data cercata non inserire il mezzo
                            
                            $dt__ = isset($row_data[2]) ? $row_data[2] : null;
                            if (!$dt__ || !$this->checkCapMessageDate($row_data[2], $dt, $dt_to)) {
                                continue;
                            }
                            
                            $vehicles[] = [
                                'cap_identifier' => $cap_message->identifier,
                                'targa' => isset($row_data[0]) ? $this->replaceDashAndBar($row_data[0]) : '',
                                'modello' => isset($row_data[1]) ? $this->replaceDashAndBar($row_data[1]) : '',
                                'data_1' => isset($row_data[2]) ? $this->getCapMessageDate($row_data[2]) : '',
                                'data_2' => isset($row_data[3]) ? $this->getCapMessageDate($row_data[3]) : '',
                                'data_3' => isset($row_data[4]) ? $this->getCapMessageDate($row_data[4]) : '',
                                'data_4' => isset($row_data[5]) ? $this->getCapMessageDate($row_data[5]) : '',
                                'codice' => isset($row_data[6]) ? $this->replaceDashAndBar($row_data[6]) : '',
                            ];
                        }
                    }
                }
            }

            $elemento['veicoli_cap'] = $vehicles;
            $array_risultati[] = $elemento;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $array_risultati,
            'pagination' => false
        ]);

        return $this->render('monitoraggio_eventi', [
            'dataProvider' => $dataProvider,
            'filter_model' => $filter_model
        ]);
    }
}
