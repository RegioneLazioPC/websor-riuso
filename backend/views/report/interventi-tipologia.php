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

$this->title = 'Report interventi per tipologia';
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
        'stato_ingaggio' => true,
        'tipologia' => true,
        'sottotipologia' => true,
    ]);

    $cols = [
        [
            'attribute' => 'tipologia',
            'label' => 'Tipologia',
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'sottotipologia',
            'label' => 'Sottotipologia',
            'contentOptions' => ['style' => 'width: 200px;']
        ],
        [
            'attribute' => 'totale',
            'label' => "Totale",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'chiuso',
            'label' => "Chiuso",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'rifiutato',
            'label' => "Totale rifiutato",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'in_attesa',
            'label' => "In attesa di conferma",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'confermato',
            'label' => "Confermato",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'fuori_orario',
            'label' => "RIFIUTATO - FUORI ORARIO",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'non_risponde',
            'label' => "RIFIUTATO - NON RISPONDE",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'mezzo_non_disponibile',
            'label' => "RIFIUTATO - MEZZO NON DISPONIBILE",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'squadra_non_disponibile',
            'label' => "RIFIUTATO - SQUADRA NON DISPONIBILE",
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'attribute' => 'altro',
            'label' => "RIFIUTATO - ALTRO",
            'contentOptions' => ['style' => 'width: 80px;']
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