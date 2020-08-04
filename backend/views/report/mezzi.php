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

$this->title = 'Report mezzi';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-index">

        <?php 

        
        
        $cols = [
            [
                'attribute' => 'anno',
                'label' => 'Anno',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'mese',
                'label' => 'Mese',
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) {
                    return (empty($model['mese'])) ? '' : $model['mese'];
                }
            ],
            [
                'attribute' => 'giorno',
                'label' => 'Giorno',
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) {
                    return (empty($model['giorno'])) ? '' : $model['giorno'];
                }
            ]
        ];

        $filter_types = [];
        foreach ($tipologie as $tipologia) {
            $filter_types[] = $tipologia;
            $cols[] = [
                'attribute' => $tipologia,
                'label' => $tipologia,
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) use ($tipologia) {
                    return (empty($model[$tipologia])) ? 0 : $model[$tipologia];
                }
            ];
        }

        echo $this->render('_search_partial_report', [
            'filter_model' => $filter_model,
            'year'=>true,
            'month'=>true,
            'pr' => true,
            'comune' => true,
            'from' => true,
            'to' => true,
            'tipo_mezzo' => true,
            'tipologia' => true,
            'sottotipologia' => true,
        ]);

        $cols[] = [
                'attribute' => 'totale',
                'label' => 'Totale',
                'contentOptions' => ['style'=>'width: 80px;']
            ];

        ?>

       <?php echo GridView::widget([
        'id' => 'report-eventi',
        'dataProvider' => $dataProvider,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'toggleData'=>false,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=> "Scarica report completo " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'onRenderSheet' => function($sheet, $widget) {
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
        'columns' => $cols
    ]); ?>
</div>
