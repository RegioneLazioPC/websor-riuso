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

use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monitoraggio realtime';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-index">

        <?php

        $cols = [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:100px;'],
                'contentOptions' => ['style' => 'width: 100px;'],
                'label' => 'ID evento'
            ],
            [
                'attribute' => 'comune',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'label' => 'Comune'
            ],
            [
                'attribute' => 'provincia',
                'headerOptions' => ['style' => 'width:80px;'],
                'contentOptions' => ['style' => 'width: 80px;'],
                'label' => 'Provincia'
            ],
            [
                'attribute' => 'indirizzo',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'label' => 'Indirizzo'
            ],
            [
                'attribute' => 'dos',
                'label' => 'DOS',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'value' => function ($data) {
                    return implode("<br />", array_map(function ($dos) {
                        return $dos['codice'];
                    }, $data['dos']));
                }
            ],
            [
                'attribute' => 'elicotteri',
                'label' => 'Elicotteri',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'value' => function ($data) {
                    return implode("<br />", array_map(function ($elicottero) {
                        return $elicottero['codice'] . " " . $elicottero['elicottero'] ;
                    }, $data['elicotteri']));
                }
            ],
            [
                'attribute' => 'canadair',
                'label' => 'COAU',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'value' => function ($data) {
                    return implode("<br />", array_map(function ($canadair) {
                        return $canadair['codice'] ;
                    }, $data['canadair']));
                }
            ],
            [
                'attribute' => 'attivazioni',
                'label' => 'Attivazioni',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'value' => function ($data) {
                    return implode("<br />", array_map(function ($a) {
                        return "<b>ODV:</b> " . $a['odv'] . " ".$a['identificativo']." ".$a['tipologia'];
                    }, $data['attivazioni']));
                }
            ],
            [
                'attribute' => 'veicoli_cap',
                'label' => 'Veicoli da cap',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:150px;'],
                'contentOptions' => ['style' => 'width: 150px;'],
                'value' => function ($data) {
                    return implode("<br />", array_map(function ($a) {
                        $link = Yii::$app->user->can('viewCapMessage') ? Html::a($a['cap_identifier'], ['/cap/single-message', 'id' => $a['cap_id']], ['target'=>'_blank']) : $a['cap_identifier'];
                        return  $link . " ".$a['targa']." ".$a['modello'];
                    }, $data['veicoli_cap']));
                }
            ],
        ];



?>
        <h4 style="margin-bottom: 18px;">Monitoraggio eventi realtime</h4>
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
    'containerOptions' => [
        'style'=>'height: 80vh; overflow: auto;'
    ],
    'floatOverflowContainer' => true,
    'export' => Yii::$app->user->can('exportData') ? [] : false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=> "Scarica report completo " . ExportMenu::widget([
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
        'footer'=>true,
    ],
    'pjax'=>true,
    'pjaxSettings'=>[
        'neverTimeout'=> true,
    ],
    'export'=> false,
    'columns' => $cols
]); ?>
</div>
