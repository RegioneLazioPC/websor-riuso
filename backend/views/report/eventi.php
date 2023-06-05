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

$this->title = 'Report eventi';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="utl-evento-index">

    <?php
    $array_tipologie = [];
    $array_figlie = [];

    $tipologie_genitori = UtlTipologia::find()->where('idparent is null')->all();
    foreach ($tipologie_genitori as $tipologia) {
        $array_tipologie[$tipologia->id] = [
            'id' => $tipologia->id,
            'tipologia' => $tipologia->tipologia,
            'children' => []
        ];
    }

    $tipologie_figlie = UtlTipologia::find()->where('idparent is not null')->all();

    foreach ($tipologie_figlie as $tipologia) {
        $array_tipologie[$tipologia->idparent]['children'][$tipologia->id] = [
            'id' => $tipologia->id,
            'tipologia' => $tipologia->tipologia
        ];
        // prendi il parent
        $array_figlie[$tipologia->id] = $tipologia->idparent;
    }

    echo $this->render('_search_partial_report', [
        'filter_model' => $filter_model,
        'year' => true,
        'month' => true,
        'pr' => Yii::$app->FilteredActions->showFieldProvincia,
        'comune' => Yii::$app->FilteredActions->showFieldComune,
        'from' => true,
        'to' => true,
        'tipologia' => true,
        'sottotipologia' => true,
    ]);


    $cols = [
        [
            'visible' => Yii::$app->FilteredActions->showFieldProvincia,
            'attribute' => 'provincia',
            'label' => 'Provincia',
            'contentOptions' => ['style' => 'width: 80px;']
        ],
        [
            'visible' => Yii::$app->FilteredActions->showFieldComune,
            'attribute' => 'comune',
            'label' => 'Comune',
            'contentOptions' => ['style' => 'width: 200px;']
        ],
        [
            'attribute' => 'totale',
            'label' => "Totale",
            'contentOptions' => ['style' => 'width: 80px;']
        ]
    ];

    $total_eventi = 0;

    foreach ($dataProvider->allModels as $row) {
        if (!empty($row['comune'])) $total_eventi += $row['totale'];
    }

    $filter_types = false;
    $filter_subtypes = false;

    $id_tipologia = null;
    $id_sottotipologia = null;

    $params = Yii::$app->request->get('FilterModel');

    if (!empty($params['tipologia'])) {
        $id_tipologia = $params['tipologia'];
        $filter_types = true;
    }

    if (!empty($params['sottotipologia'])) {
        if (!empty($params['tipologia'])) $id_tipologia = $array_figlie[$params['sottotipologia']];
        $id_sottotipologia = $params['sottotipologia'];
        $filter_types = true;
        $filter_subtypes = true;
    }

    foreach ($array_tipologie as $tipologia) {

        if ($filter_types && $tipologia['id'] != $id_tipologia) continue;

        foreach ($tipologia['children'] as $figlia) {

            if ($filter_subtypes && $figlia['id'] != $id_sottotipologia) continue;

            $cols[] = [
                'attribute' => 'totale_' . $figlia['id'],
                'label' => $figlia['tipologia'] . " (" . $tipologia['tipologia'] . ")",
                'contentOptions' => ['style' => 'width: 80px;'],
                'value' => function ($data) use ($figlia) {
                    return (isset($data['totale_' . $figlia['id']])) ? $data['totale_' . $figlia['id']] : 0;
                }
            ];
        }

        $cols[] = [
            'attribute' => 'totale_' . $tipologia['id'],
            'label' => "Totale " . $tipologia['tipologia'],
            'contentOptions' => ['style' => 'width: 80px;'],
            'value' => function ($data) use ($tipologia) {
                return (isset($data['totale_' . $tipologia['id']])) ? $data['totale_' . $tipologia['id']] : 0;
            }
        ];
    }

    echo $this->render('_export_pdf', [
        'cols' => $cols
    ]);

    ?>

    <?php echo GridView::widget([
        'id' => 'report-eventi',
        'dataProvider' => $dataProvider,
        'summary' => 'Totale eventi: ' . $total_eventi,
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