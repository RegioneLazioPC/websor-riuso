<?php

use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use kartik\export\ExportMenu;

use common\models\LocComune;
use common\models\UtlIngaggio;

use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlAggregatoreTipologie;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Report interventi rifiutati';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-index">

    <?php

    $stati = [];

    echo $this->render('_search_partial_report', [
        'filter_model' => $filter_model,
        'year' => true,
        'month' => true,
        'pr' => Yii::$app->FilteredActions->showFieldProvincia,
        'comune' => Yii::$app->FilteredActions->showFieldComune,
        'from' => true,
        'to' => true,
        'odv' => true
    ]);

    $cols = [
        [
            'attribute' => 'num_protocollo',
            'label' => 'Protocollo websor',
            'contentOptions' => ['style' => 'width: 120px;']
        ], [
            'attribute' => 'created_at',
            'label' => 'Giorno',
            'contentOptions' => ['style' => 'width: 100px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data['created_at']);
            }
        ], [
            'attribute' => 'created_at',
            'label' => 'Ora',
            'contentOptions' => ['style' => 'width: 100px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->astime($data['created_at']);
            }
        ], [
            'attribute' => 'num_elenco_territoriale',
            'label' => 'Numero elenco territoriale',
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'denominazione',
            'label' => 'Organizzazione',
            'width' => 200,
            'headerOptions' => ['style' => 'width:200px;'],
            'contentOptions' => ['style' => 'width: 200px;white-space: normal;']
        ],
        [
            'attribute' => 'tipologia',
            'label' => 'Tipo di intervento richiesto',
            'contentOptions' => ['style' => 'width: 150px;']
        ],
        [
            'attribute' => 'targa',
            'label' => 'Targa mezzo attivato',
            'contentOptions' => ['style' => 'width: 150px;']
        ],
        [
            'attribute' => 'motivazione_rifiuto',
            'label' => 'Motivo del rifiuto',
            'contentOptions' => ['style' => 'width: 150px;'],
            'value' => function ($data) {
                return UtlIngaggio::replaceMotivazioneRifiuto($data['motivazione_rifiuto']);
            }
        ]
    ];

    echo $this->render('_export_pdf', [
        'cols' => $cols
    ]);

    ?>

    <?php echo GridView::widget([
        'id' => 'report-eventi',
        'dataProvider' => $dataProvider,
        'responsive' => true,
        'hover' => true,
        'toggleData' => false,
        'floatHeader' => true,
        'containerOptions' => [
            'class' => 'overflow-table'
        ],
        'floatOverflowContainer' => true,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'panel' => [
            'heading' => "Scarica report completo " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'onRenderSheet' => function ($sheet, $widget) {
                    $sheet->setTitle("ExportWorksheet");
                },
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]
            ]),
            'footer' => true,
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'export' => false,
        'columns' => $cols
    ]); ?>
</div>