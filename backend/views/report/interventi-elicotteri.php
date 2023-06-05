<?php

use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use kartik\export\ExportMenu;

use common\models\LocComune;
use common\models\UtlIngaggio;

use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlAggregatoreTipologie;

use common\models\reportistica\ViewReportAttivazioni;

$this->title = 'Report interventi elicotteri';
$this->params['breadcrumbs'][] = $this->title;


$cols = [
    [
        'attribute' => 'id_evento',
        'label' => 'ID EVENTO',
        'contentOptions' => ['style' => 'width: 80px;']
    ],
    [
        'attribute' => 'num_protocollo',
        'label' => 'NUM. PROTOCOLLO',
        'width' => 150,
        'filter' => false,
        'headerOptions' => ['style' => 'width:150px;'],
        'contentOptions' => ['style' => 'width: 150px;white-space: normal;']
    ],
    [
        'attribute' => 'elicottero',
        'label' => 'ELICOTTERO',
        'width' => 150,
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(
            \common\models\UtlAutomezzo::find()->joinWith(['tipo'])
                ->where(['UPPER(utl_automezzo_tipo.descrizione)' => 'ELICOTTERO'])
                ->orderBy(['targa' => SORT_ASC])->asArray()->all(),
            'targa',
            'targa'
        ),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true,
            ]
        ],
        'headerOptions' => ['style' => 'width:150px;'],
        'contentOptions' => ['style' => 'width: 150px;white-space: normal;']
    ],
    [
        'visible' => Yii::$app->FilteredActions->showFieldComune,
        'attribute' => 'id_comune',
        'label' => 'COMUNE',
        'width' => '300px',
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(
            \common\models\LocComune::find()
                ->where([
                    Yii::$app->params['region_filter_operator'],
                    'id_regione',
                    Yii::$app->params['region_filter_id']
                ])
                ->orderBy([
                    'comune' => SORT_ASC,
                ])
                ->all(),
            'id',
            'comune'
        ),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true,
            ]
        ],
        'headerOptions' => ['style' => 'width:300px;'],
        'contentOptions' => ['style' => 'width: 300px;white-space: normal;'],
        'value' => function ($data) {
            return $data->comune;
        }
    ],
    [
        'visible' => Yii::$app->FilteredActions->showFieldProvincia,
        'attribute' => 'sigla_provincia',
        'label' => 'PROVINCIA',
        'width' => 150,
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(LocProvincia::find()
            ->where([
                Yii::$app->params['region_filter_operator'],
                'id_regione',
                Yii::$app->params['region_filter_id']
            ])
            ->all(), 'sigla', 'sigla'),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true,
            ]
        ],
        'headerOptions' => ['style' => 'width:150px;'],
        'contentOptions' => ['style' => 'width: 150px;white-space: normal;']
    ],
    [
        'attribute' => 'n_lanci',
        'label' => "Num. lanci",
        'filter' => false,
        'contentOptions' => ['style' => 'width: 150px;']
    ],
    [
        'attribute' => 'dataora_decollo',
        'label' => "Ora decollo",
        'format' => 'datetime',
        'filter' => false,
        'contentOptions' => ['style' => 'width: 150px;']
    ],
    [
        'attribute' => 'dataora_atterraggio',
        'label' => "Ora atterraggio",
        'format' => 'datetime',
        'filter' => false,
        'contentOptions' => ['style' => 'width: 150px;']
    ],
    [
        'attribute' => 'data_attivazione',
        'label' => "Data",
        'format' => 'date',
        'filter' => false,
        'contentOptions' => ['style' => 'width: 120px;']
    ],
    [
        'attribute' => 'tempo_volo',
        'label' => "Tempo di volo",
        'filter' => false,
        'contentOptions' => ['style' => 'width: 120px;']
    ]
];

$js = '$("#lista-ingaggi-pjax").on("pjax:end", function() {
           jQuery("#utlingaggiosearch-data_dal-kvdate").kvDatepicker({
                language: "it",
                format: "dd-mm-yyyy",
                todayHighlight: true,
                autoclose: true
            })
           jQuery("#utlingaggiosearch-data_al-kvdate").kvDatepicker({
                language: "it",
                format: "dd-mm-yyyy",
                todayHighlight: true,
                autoclose: true
            })
       });';

$this->registerJs($js, $this::POS_READY);


echo $this->render('_export_pdf', [
    'cols' => $cols
]);

?>
<div class="utl-evento-index">
    <?= GridView::widget([
        'id' => 'lista-ingaggi',
        'dataProvider' => $ingaggiDataProvider,
        'filterModel' => $ingaggiSearchModel,
        'responsive' => true,
        'hover' => true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'toggleData' => false,
        'export' => false, //Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'panel' => [
            'heading' => "Scarica report completo " . ExportMenu::widget([
                'dataProvider' => $ingaggiDataProvider,
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
            'before' => $this->render('_search_interventi_elicotteri', [
                'model' => $ingaggiSearchModel,
                'view' => 'view'
            ]),
            'footer' => true,
        ],
        'pjax' => false,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        //'export'=> true,
        'columns' => $cols,
    ]); ?>
</div>