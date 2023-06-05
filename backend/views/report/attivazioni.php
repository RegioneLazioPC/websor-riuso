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
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Report attivazioni';
$this->params['breadcrumbs'][] = $this->title;


$sottotipologie = (!empty($ingaggiSearchModel->tipologia)) ?
    ArrayHelper::map(
        UtlTipologia::find()
            ->joinWith('tipologiaGenitore as genitore')
            ->where(['genitore.tipologia' => $ingaggiSearchModel->tipologia]) // is not null')
            ->orderBy(['idparent' => SORT_ASC])->asArray()->all(),
        'tipologia',
        'tipologia'
    )
    : [];



$cols = [
    [
        'attribute' => 'created_at',
        'label' => 'Inizio',
        'format' => 'raw',
        'filterType' => GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => 1,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true,
            ]
        ],
        'contentOptions' => ['style' => 'width: 80px;']
    ],
    [
        'attribute' => 'closed_at',
        'label' => 'Fine',
        'format' => 'raw',
        'filterType' => GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => 1,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true,
            ]
        ],
        'contentOptions' => ['style' => 'width: 80px;'],
        'value' => function ($model) {
            return ($model['stato'] == 'Rifiutato' && empty($model['closed_at'])) ? $model['created_at'] : $model['closed_at'];
        }
    ],
    [
        'label' => 'Durata',
        'attribute' => 'durata'
    ],
    [
        'attribute' => 'mese',
        'label' => 'Mese',
        'contentOptions' => ['style' => 'width: 120px;'],
        'filter' => Html::activeDropDownList($ingaggiSearchModel, 'mese', [
            1 => 'Gennaio',
            2 => 'Febbraio',
            3 => 'Marzo',
            4 => 'Aprile',
            5 => 'Maggio',
            6 => 'Giugno',
            7 => 'Luglio',
            8 => 'Agosto',
            9 => 'Settembre',
            10 => 'Ottobre',
            11 => 'Novembre',
            12 => 'Dicembre'
        ], ['class' => 'form-control', 'prompt' => 'seleziona'])
    ],
    [
        'attribute' => 'anno',
        'label' => 'Anno',
        'contentOptions' => ['style' => 'width: 120px;']
    ],
    [
        'attribute' => 'num_protocollo',
        'label' => 'N.Protocollo',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width: 80px;'],
        'value' => function ($data) {
            return '<a href="'
                . Url::toRoute(['evento/view', 'id' => $data['id_evento']]) .
                '" target="_blank">' . Html::encode($data['num_protocollo']) . '</a>';
        }
    ],
    [
        'attribute' => 'tipologia',
        'label' => 'Tipologia',
        'contentOptions' => ['style' => 'width: 80px;'],
        'filter' => Html::activeDropDownList(
            $ingaggiSearchModel,
            'tipologia',
            array_merge(['__' => 'Sos'], ArrayHelper::map(UtlTipologia::find()->where('idparent is null')->asArray()->all(), 'tipologia', 'tipologia')),
            ['class' => 'form-control', 'prompt' => 'Tutti']
        )
    ],
    [
        'attribute' => 'sottotipologia',
        'label' => 'Sottotipologia',
        'contentOptions' => ['style' => 'width: 80px;'],
        'filter' => Html::activeDropDownList(
            $ingaggiSearchModel,
            'sottotipologia',
            $sottotipologie,
            ['class' => 'form-control', 'prompt' => 'Tutti']
        )
    ],
    [
        'label' => 'Gestore',
        'attribute' => 'id_gestore',
        'filter' => Html::activeDropDownList($ingaggiSearchModel, 'id_gestore', ArrayHelper::map(\common\models\EvtGestoreEvento::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
        'value' => function ($data) {
            return @$data->gestore;
        }
    ],
    [
        'label' => 'COC',
        'attribute' => 'coc',
        'filter' => Html::activeDropDownList($ingaggiSearchModel, 'coc', ['No' => 'No', 'Si' => 'Si'], ['class' => 'form-control', 'prompt' => 'Tutti'])
    ],
    [
        'label' => 'Indirizzo/luogo',
        'attribute' => 'indirizzo',
    ],
    [
        'visible' => Yii::$app->FilteredActions->showFieldComune,
        'label' => 'Comune',
        'attribute' => 'comune',
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(LocComune::find()
            ->where([
                Yii::$app->params['region_filter_operator'],
                'id_regione',
                Yii::$app->params['region_filter_id']
            ])
            ->all(), 'id', 'comune'),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true,
            ]
        ],

    ],
    [
        'visible' => Yii::$app->FilteredActions->showFieldProvincia,
        'label' => 'Provincia',
        'attribute' => 'provincia_sigla',
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(LocProvincia::find()
            ->where([
                Yii::$app->params['region_filter_operator'],
                'id_regione',
                Yii::$app->params['region_filter_id']
            ])
            ->all(), 'id', 'sigla'),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true,
            ]
        ],
    ],

    [
        'label' => 'Mezzo',
        'attribute' => 'tipo_automezzo',
        'filter' => Html::activeDropDownList(
            $ingaggiSearchModel,
            'tipo_automezzo',
            ArrayHelper::map(UtlAutomezzoTipo::find()->all(), 'id', 'descrizione'),
            ['class' => 'form-control', 'prompt' => 'Tutti']
        ),
    ],
    [
        'attribute' => 'targa',
        'label' => 'Targa'
    ],
    [
        'label' => 'Attrezzatura',
        'attribute' => 'tipo_attrezzatura',
        'filter' => Html::activeDropDownList(
            $ingaggiSearchModel,
            'tipo_attrezzatura',
            ArrayHelper::map(UtlAttrezzaturaTipo::find()->all(), 'id', 'descrizione'),
            ['class' => 'form-control', 'prompt' => 'Tutti']
        ),

    ],
    [
        'label' => 'Tipologia mezzo/attrezzatura',
        'attribute' => 'aggregatore',
        'filter' => Html::activeDropDownList($ingaggiSearchModel, 'aggregatore', ArrayHelper::map(UtlAggregatoreTipologie::find()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
        'value' => function ($data) {
            $ret = [];
            if (!empty($data->aggregatore_automezzi)) return $data->aggregatore_automezzi;
            if (!empty($data->aggregatore_attrezzature)) return $data->aggregatore_attrezzature;

            return "";
        }
    ],
    [
        'label' => 'Identificativo organizzazione',
        'attribute' => 'num_elenco_territoriale'
    ],
    [
        'label' => 'Organizzazione',
        'attribute' => 'organizzazione'
    ],
    [
        'label' => 'Sede',
        'attribute' => 'indirizzo_sede'
    ],
    [
        'label' => 'Tipo sede',
        'attribute' => 'tipo_sede'
    ],

    [
        'attribute' => 'stato',
        'contentOptions' => ['style' => 'max-width: 200px; white-space: normal; word-wrap: break-word;'],
        'filter' => Html::activeDropDownList($ingaggiSearchModel, 'stato', ViewReportAttivazioni::getStati(), ['class' => 'form-control', 'prompt' => 'Tutti'])
    ],
    [
        'attribute' => 'note',
        'contentOptions' => ['style' => 'max-width: 200px; white-space: normal; word-wrap: break-word;']
    ],
    [
        'attribute' => 'lat',
        'label' => 'Lat WGS84',
        'contentOptions' => ['style' => 'width: 80px;']
    ],
    [
        'attribute' => 'lon',
        'label' => 'Lon WGS84',
        'contentOptions' => ['style' => 'width: 80px;']
    ],
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
            'before' => $this->render('_search_attivazioni', [
                'model' => $ingaggiSearchModel,
                'view' => 'view'
            ]),
            'footer' => true,
        ],
        'pjax' => false,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'rowOptions' => function ($model) {
            $class = null;
            return ['class' => $model->getStatoColor() . '-td'];
        },
        'columns' => $cols,
    ]); ?>
</div>