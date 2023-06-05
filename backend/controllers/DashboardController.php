<?php

namespace backend\controllers;

use Exception;
use Yii;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\base\Security;
use yii\data\ArrayDataProvider;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;

class DashboardController extends Controller
{
    protected $colors = [
            '#014464',
            '#6294c1',
            '#ec282d',
            '#f39c12',
            '#4caf50',
            '#9c27b0',
            '#9e9e9e',
            '#795548',
            '#00bcd4',
            '#607d8b',
            '#2196f3',
            '#8bc34a',
            '#867542',
            '#9e9e9e',
            '#8e99da'
        ];
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
                        Yii::error(json_encode(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId())));
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['fine-giornata', 'export-excel', 'export-pdf'],
                        'permissions' => ['viewDashboard']
                    ]
                ],

            ],
        ];
    }

    /**
     * Lists all UtlUtente models.
     * @return mixed
     */
    public function actionFineGiornata()
    {
        return $this->render(
            'fine_giornata',
            array_merge($this->getData(), ['colors' => $this->colors])
        );
    }


    public function actionExportExcel()
    {
        $data = $this->getData();

        $center = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        $right = [
            'alignment' => [
                'right' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        $left = [
            'alignment' => [
                'left' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        $bold = [
            'font'  => [
                'bold'  =>  true
            ],
        ];

        $font_big = [
            'font'  => [
                'bold'  =>  true,
                'size' => 18,
                'color' =>   array('rgb' => '000000')
            ],
        ];

        $font_really_big = [
            'font'  => [
                'bold'  =>  true,
                'size' => 24,
                'color' =>   array('rgb' => '000000')
            ],
        ];

        $bordered = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ]
        ];
        
        $today = new \DateTime();
        $yesterday = (new \DateTime)->sub(new \DateInterval('P1D'));


        $spread = new Spreadsheet();
        $eventi_attivi_t = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Eventi aperti-tipologia');
        $eventi_attivi_p = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Eventi aperti-provincia');
        $eventi_aperti = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Eventi aperti');
        $eventi_chiusi = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Eventi chiusi');

        $incendi_attivi_t = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Incendi aperti-sottotipologia');
        $incendi_attivi_ente = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Incendi aperti-ente gestore');
        $incendi_provincia = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Incendi aperti-provincia');
        $incendi_boschivi_provincia = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Incendi boschivi-provincia');

        $altro = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spread, 'Altro');

        $spread->addSheet($eventi_attivi_t, 0);
        $spread->addSheet($eventi_attivi_p, 1);
        $spread->addSheet($eventi_aperti, 2);
        $spread->addSheet($eventi_chiusi, 3);

        $spread->addSheet($incendi_attivi_t, 4);
        $spread->addSheet($incendi_attivi_ente, 5);
        $spread->addSheet($incendi_provincia, 6);
        $spread->addSheet($incendi_boschivi_provincia, 7);

        $spread->addSheet($altro, 8);

        $spread->setActiveSheetIndex(0);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Eventi aperti il ' . $today->format('d/m/Y') . ' / tipologia'], null, "A1");

        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['eventi_attivi_tipologia'] as $row) {
            $sheet->fromArray([
                $row['tipologia'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }

        $spread->setActiveSheetIndex(1);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Eventi aperti il ' . $today->format('d/m/Y') . ' / provincia'], null, "A1");

        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['eventi_attivi_provincia'] as $row) {
            $sheet->fromArray([
                $row['sigla'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }



        $spread->setActiveSheetIndex(2);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Eventi aperti'], null, "A1");

        $sheet->fromArray([], null, "A2");
        $sheet->fromArray([
                $today->format('d-m-Y')
            ], null, "A3");
        $sheet->fromArray([
                "".$data['eventi_aperti'][0]['n'],
                ""
            ], null, "A4");
        $sheet->fromArray([
                "".$data['eventi_aperti'][1]['n'],
                "Eventi attivi alle " . $today->format('H:i')
            ], null, "A5");


        $spread->setActiveSheetIndex(3);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Eventi chiusi'], null, "A1");

        $sheet->fromArray([], null, "A2");
        $sheet->fromArray([
                $today->format('d-m-Y')
            ], null, "A3");
        $sheet->fromArray([
                "".$data['eventi_chiusi'][0]['n']
            ], null, "A4");



        $spread->setActiveSheetIndex(4);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Incendi aperti il ' . $today->format('d/m/Y') . ' / sottotipologia'], null, "A1");
        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['incendi_attivi_tipologia'] as $row) {
            $sheet->fromArray([
                $row['tipologia'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }
        

        $spread->setActiveSheetIndex(5);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Incendi aperti il ' . $today->format('d/m/Y') . ' / ente gestore'], null, "A1");
        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['incendi_attivi_ente_gestore'] as $row) {
            $sheet->fromArray([
                $row['descrizione'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }
        

        $spread->setActiveSheetIndex(6);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Incendi aperti il ' . $today->format('d/m/Y') . ' / provincia'], null, "A1");
        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['incendi_provincia'] as $row) {
            $sheet->fromArray([
                $row['sigla'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }


        $spread->setActiveSheetIndex(7);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray(['Incendi boschivi aperti il ' . $today->format('d/m/Y') . ' / provincia'], null, "A1");
        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['incendi_boschivi_provincia'] as $row) {
            $sheet->fromArray([
                $row['sigla'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }


        $spread->setActiveSheetIndex(8);
        $sheet = $spread->getActiveSheet();

        $sheet->getStyle('A1')->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray([
            'Mezzi impiegati',
            //'Incendi con intervento del mezzo aereo',
            //'Numero lanci elicotteri',
            //'Ore di volo'
        ], null, "A1");
        $sheet->fromArray([], null, "A2");
        $n = 3;
        foreach ($data['mezzi_impiegati'] as $row) {
            $sheet->fromArray([
                $row['sigla'],
                "".$row['conteggio']
            ], null, "A".$n);
            $n++;
        }

        $sheet->fromArray([], null, "A".$n);
        $n++;

        $sheet->getStyle('A'.$n)->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->getStyle('B'.$n)->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->getStyle('C'.$n)->applyFromArray(array_merge($font_big, $right, $bold));
        $sheet->fromArray([
            'Incendi con intervento del mezzo aereo',
            'Numero lanci elicotteri',
            'Ore di volo'
        ], null, "A".$n);
        $n++;
        $sheet->fromArray([
            "".$data['incendi_mezzo_aereo'][0]['conteggio'],
            "".$data['lanci_elicottero'][0]['numero_lanci'],
            "".$data['ore_di_volo'][0]['ore_di_volo']
        ], null, "A".$n);

        $spread->removeSheetByIndex(9);
        $spread->setActiveSheetIndex(0);
        
        $nome_file = "DATI_FINE_GIORNATA_" . $today->format('Y_m_d');

        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: GET');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$nome_file.'.xlsx";');
        header('Content-Transfer-Encoding: binary');

        $writer = new Xlsx($spread);
        $writer->save('php://output', 'xlsx');
        exit(0);
    }



    private function getData()
    {
        $eventi_attivi_tipologia = Yii::$app->db->createCommand("SELECT t.tipologia, count(e.id) as conteggio
            FROM utl_tipologia t 
            LEFT JOIN utl_evento e ON e.tipologia_evento = t.id AND e.dataora_evento::date = CURRENT_DATE AND e.idparent is null
            WHERE t.idparent is null
            GROUP BY t.id
            ORDER BY t.tipologia ASC;")->queryAll();

        $eventi_attivi_provincia = Yii::$app->db->createCommand("SELECT p.sigla, count(e.id) FILTER (WHERE c.id_provincia = p.id)  as conteggio
            FROM loc_provincia p
            LEFT JOIN utl_evento e ON e.dataora_evento::date = CURRENT_DATE AND e.idparent is null
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            WHERE p.id_regione = ".intval(Yii::$app->params['region_filter_id'])."
            GROUP BY p.id
            ORDER BY p.sigla ASC;")->queryAll();

        $eventi_aperti = Yii::$app->db->createCommand("SELECT count(*) as n, 'today' as ref
            FROM utl_evento 
            WHERE dataora_evento::date = CURRENT_DATE AND idparent is null
            UNION 
            SELECT count(*) as n, 'empty' as ref
            FROM utl_evento 
            WHERE stato <> 'Chiuso' AND idparent is null
            ;")->queryAll();

        $eventi_chiusi = Yii::$app->db->createCommand("SELECT count(*) as n, 'today' as ref
            FROM utl_evento 
            WHERE closed_at::date = CURRENT_DATE AND idparent is null
            ;")->queryAll();


        // AIB
        $incendi_attivi_tipologia = Yii::$app->db->createCommand("SELECT t.tipologia, count(e.id) as conteggio
            FROM utl_tipologia t 
            LEFT JOIN utl_tipologia parent ON parent.id = t.idparent
            LEFT JOIN utl_evento e ON e.sottotipologia_evento = t.id AND 
                (
                    e.dataora_evento::date = CURRENT_DATE OR 
                    (e.dataora_evento::date < CURRENT_DATE AND (e.closed_at is null OR e.closed_at::date = CURRENT_DATE) )
                ) AND 
                e.idparent is null
            WHERE parent.tipologia ilike 'incendio'
            GROUP BY t.id
            ORDER BY t.tipologia ASC;")->queryAll();

        $incendi_attivi_ente_gestore = Yii::$app->db->createCommand("SELECT g.descrizione, count(e.id) as conteggio 
            FROM evt_gestore_evento g
            LEFT JOIN (SELECT utl_evento.id, utl_evento.id_gestore_evento FROM utl_evento
                LEFT JOIN utl_tipologia t ON t.id = tipologia_evento
                WHERE t.tipologia ilike 'incendio' AND 
                (
                    utl_evento.dataora_evento::date = CURRENT_DATE OR 
                    (utl_evento.dataora_evento::date < CURRENT_DATE AND (utl_evento.closed_at is null OR utl_evento.closed_at::date = CURRENT_DATE) )
                ) AND utl_evento.idparent is null
            ) e ON 
                e.id_gestore_evento = g.id
                        GROUP BY g.id
                        ORDER BY g.descrizione ASC;")->queryAll();

        $incendi_provincia = Yii::$app->db->createCommand("SELECT p.sigla, count(e.id) FILTER (WHERE c.id_provincia = p.id AND t.tipologia ilike 'incendio') as conteggio
            FROM loc_provincia p
            LEFT JOIN utl_evento e ON (
                    e.dataora_evento::date = CURRENT_DATE OR 
                    (e.dataora_evento::date < CURRENT_DATE AND (e.closed_at is null OR e.closed_at::date = CURRENT_DATE) )
                ) AND e.idparent is null 
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            WHERE p.id_regione = ".intval(Yii::$app->params['region_filter_id'])."
            GROUP BY p.id
            ORDER BY p.sigla ASC;")->queryAll();

        $incendi_boschivi_provincia = Yii::$app->db->createCommand("SELECT p.sigla, count(e.id) FILTER (WHERE c.id_provincia = p.id AND t.tipologia ilike 'incendio' AND m.tipologia ilike 'boschivo') as conteggio
            FROM loc_provincia p
            LEFT JOIN utl_evento e ON (
                    e.dataora_evento::date = CURRENT_DATE OR 
                    (e.dataora_evento::date < CURRENT_DATE AND (e.closed_at is null OR e.closed_at::date = CURRENT_DATE) )
                ) AND e.idparent is null 
            LEFT JOIN utl_tipologia t ON t.id = e.tipologia_evento
            LEFT JOIN utl_tipologia m ON m.id = e.sottotipologia_evento
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            WHERE p.id_regione = ".intval(Yii::$app->params['region_filter_id'])."
            GROUP BY p.id
            ORDER BY p.sigla ASC;")->queryAll();

        $mezzi_impiegati = Yii::$app->db->createCommand("SELECT p.sigla, count(i.id) FILTER (WHERE i.created_at::date = CURRENT_DATE AND c.id_provincia = p.id AND 
                (t.descrizione ilike '%autobotte%' OR t.descrizione ilike '%pickup%' OR t.descrizione ilike '%pick up%')
            ) as conteggio
            FROM loc_provincia p
            LEFT JOIN utl_ingaggio i ON i.id is not null
            LEFT JOIN utl_evento e ON i.idevento = e.id 
            LEFT JOIN utl_automezzo a ON a.id = i.idautomezzo
            LEFT JOIN utl_automezzo_tipo t ON t.id = a.idtipo
            LEFT JOIN loc_comune c ON c.id = e.idcomune
            WHERE p.id_regione = ".intval(Yii::$app->params['region_filter_id'])."
            GROUP BY p.id
            ORDER BY p.sigla ASC;")->queryAll();

        $incendi_mezzo_aereo = Yii::$app->db->createCommand("SELECT count(*) as conteggio
            FROM utl_evento e 
            WHERE e.dataora_evento::date = CURRENT_DATE
            AND (
                (
                    (SELECT count(id) FROM richiesta_canadair WHERE idevento = e.id AND engaged = true) +       (SELECT count(id) FROM richiesta_elicottero WHERE idevento = e.id AND engaged = true)
                ) > 0
            );")->queryAll();

        $lanci_elicottero = Yii::$app->db->createCommand("SELECT COALESCE( sum(n_lanci), 0) as numero_lanci FROM richiesta_elicottero WHERE created_at::date = CURRENT_DATE AND engaged = true;")->queryAll();

        $ore_di_volo = Yii::$app->db->createCommand("SELECT 
             COALESCE( sum( (SELECT v.stop_local_timestamp - v.start_local_timestamp) ), '00:00:00'::interval) as ore_di_volo
             FROM utl_arka_voli v
              WHERE v.start_local_timestamp::date = CURRENT_DATE;")->queryAll();

        return [
            'eventi_attivi_tipologia' => $eventi_attivi_tipologia,
            'eventi_attivi_provincia' => $eventi_attivi_provincia,
            'eventi_aperti' => $eventi_aperti,
            'eventi_chiusi' => $eventi_chiusi,
            'incendi_attivi_tipologia' => $incendi_attivi_tipologia,
            'incendi_attivi_ente_gestore' => $incendi_attivi_ente_gestore,
            'incendi_provincia' => $incendi_provincia,
            'incendi_boschivi_provincia' => $incendi_boschivi_provincia,
            'mezzi_impiegati' => $mezzi_impiegati,
            'incendi_mezzo_aereo' => $incendi_mezzo_aereo,
            'lanci_elicottero' => $lanci_elicottero,
            'ore_di_volo' => $ore_di_volo,
        ];
    }

    /**
     * Esporta in pdf
     * @return [type] [description]
     */
    public function actionExportPdf()
    {

        $colors = $this->colors;

        //define("_JPGRAPH_PATH", __DIR__ . '/../../common/utils/chart/jpgraph/src/');

        $data = $this->getData();
        $images = [];

        $total = 0;
        foreach ($data['eventi_attivi_tipologia'] as $row) {
            $total += $row['conteggio'];
        }
        if ($total > 0) {
            $graph = new Graph\PieGraph(400, 300);
            //$graph->title->Set("EVENTI ATTIVI PER TIPOLOGIA");
        
            $graph->SetBox(true);
            $pie_data = array_map(function ($e) {
                return $e['conteggio'];
            }, $data['eventi_attivi_tipologia']);
            $p1 = new Plot\PiePlot($pie_data);
            $p1->value->setFormat('%01.2f%%');
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetSliceColors($colors);
            //$p1->value->SetFormat('$%d');

            $graph->Add($p1);

            $name = 'evt_tipo_.png';
            $fileName = __DIR__ . "/../../common/tmpchart/".$name;
            $graph->Stroke($fileName);
            $images[] = $fileName;
        } else {
            $images[] = 'empty';
        }

        

        $total = 0;
        foreach ($data['eventi_attivi_provincia'] as $row) {
            $total += $row['conteggio'];
        }
        if ($total > 0) {
            $graph = new Graph\PieGraph(400, 300);
            //$graph->title->Set("EVENTI ATTIVI PER PROVINCIA");
            
            $graph->SetBox(true);

            $pie_data = array_map(function ($e) {
                return $e['conteggio'];
            }, $data['eventi_attivi_provincia']);
            $p1 = new Plot\PiePlot($pie_data);
            $p1->value->setFormat('%01.2f%%');
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetSliceColors($colors);
            //$p1->value->SetFormat('$%d');

            $graph->Add($p1);

            $name = 'evt_pr_.png';
            $fileName = __DIR__ . "/../../common/tmpchart/".$name;
            $graph->Stroke($fileName);
            $images[] = $fileName;
        } else {
            $images[] = 'empty';
        }




        $total = 0;
        foreach ($data['incendi_attivi_tipologia'] as $row) {
            $total += $row['conteggio'];
        }
        if ($total > 0) {
            $graph = new Graph\PieGraph(400, 300);
            //$graph->title->Set("INCENDI ATTIVI PER TIPOLOGIA");
            
            $graph->SetBox(true);

            $pie_data = array_map(function ($e) {
                return $e['conteggio'];
            }, $data['incendi_attivi_tipologia']);
            $p1 = new Plot\PiePlot($pie_data);
            $p1->value->setFormat('%01.2f%%');
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetSliceColors($colors);
            //$p1->value->SetFormat('$%d');

            $graph->Add($p1);

            $name = 'inc_t_.png';
            $fileName = __DIR__ . "/../../common/tmpchart/".$name;
            $graph->Stroke($fileName);
            $images[] = $fileName;
        } else {
            $images[] = 'empty';
        }





        $total = 0;
        foreach ($data['incendi_attivi_ente_gestore'] as $row) {
            $total += $row['conteggio'];
        }
        if ($total > 0) {
            $graph = new Graph\PieGraph(400, 300);
            //$graph->title->Set("INCENDI ATTIVI PER ENTE GESTORE");
            
            $graph->SetBox(true);

            $pie_data = array_map(function ($e) {
                return $e['conteggio'];
            }, $data['incendi_attivi_ente_gestore']);
            $p1 = new Plot\PiePlot($pie_data);
            $p1->value->setFormat('%01.2f%%');
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetSliceColors($colors);
            //$p1->value->SetFormat('$%d');

            $graph->Add($p1);

            $name = 'inc_eg_.png';
            $fileName = __DIR__ . "/../../common/tmpchart/".$name;
            $graph->Stroke($fileName);
            $images[] = $fileName;
        } else {
            $images[] = 'empty';
        }






        $total = 0;
        foreach ($data['incendi_provincia'] as $row) {
            $total += $row['conteggio'];
        }
        if ($total > 0) {
            $graph = new Graph\PieGraph(400, 300);
            //$graph->title->Set("INCENDI ATTIVI PER PROVINCIA");
            
            $graph->SetBox(true);

            $pie_data = array_map(function ($e) {
                return $e['conteggio'];
            }, $data['incendi_provincia']);
            $p1 = new Plot\PiePlot($pie_data);
            $p1->value->setFormat('%01.2f%%');
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetSliceColors($colors);
            //$p1->value->SetFormat('$%d');

            $graph->Add($p1);

            $name = 'inc_pr_.png';
            $fileName = __DIR__ . "/../../common/tmpchart/".$name;
            $graph->Stroke($fileName);
            $images[] = $fileName;
        } else {
            $images[] = 'empty';
        }



        $total = 0;
        foreach ($data['incendi_boschivi_provincia'] as $row) {
            $total += $row['conteggio'];
        }
        if ($total > 0) {
            $graph = new Graph\PieGraph(400, 300);
            //$graph->title->Set("INCENDI BOSCHIVI ATTIVI PER PROVINCIA");
            
            $graph->SetBox(true);

            $pie_data = array_map(function ($e) {
                return $e['conteggio'];
            }, $data['incendi_boschivi_provincia']);
            $p1 = new Plot\PiePlot($pie_data);
            $p1->value->setFormat('%01.2f%%');
            $p1->ShowBorder();
            $p1->SetColor('black');
            $p1->SetSliceColors($colors);
            //$p1->value->SetFormat('$%d');

            $graph->Add($p1);

            $name = 'inc_b_pr_.png';
            $fileName = __DIR__ . "/../../common/tmpchart/".$name;
            $graph->Stroke($fileName);
            $images[] = $fileName;
        } else {
            $images[] = 'empty';
        }












        $content = $this->renderPartial('pdf.php', array_merge($data, [
            'colors' => $this->colors,
            'images' => $images
        ]));

        $css = "h2 {
            font-weight: bold;
            font-size: 24px;
        }
        h3{
            font-size: 18px;
            font-weight: bold;
            
        }
        h4{
            text-align: center;
            color: #333;
            font-size: 16px;
        }
        .big_number{
          font-weight: bold;
          font-size: 46px;
          text-align: center;
        }
        .main_diff{
          line-height: 64px;
          font-size: 32px;
          text-align: center;
        }
        .break{
            page-break-after: always
        }
        ";

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'filename' => 'dashboard_' . date('d-m-Y'),
            'cssInline' => $css,
            'options' => [
                'title' => 'Dashboard ' . date('d-m-Y'),
                'useGraphs' => true
            ],
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                //'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        

        return $pdf->render();
    }
}
