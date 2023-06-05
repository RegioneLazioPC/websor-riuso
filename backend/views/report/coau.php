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

$this->title = 'Report elicotteri per coau';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-index">

        <?php 

        echo $this->render('_search_partial_report', [
            'filter_model' => $filter_model,
            'pr' => true,
            'dataora'=>true
        ]);

        $child_cols = [
            [
                'attribute' => 'data_richieste',
                'label' => 'Data',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo',
                'label' => 'Incendi boschivi',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'solo_regionali',
                'label' => 'Incendi boschivi con soli aeromobili regionali',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo_chiuso',
                'label' => 'Incendi boschivi spenti',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo_aperto',
                'label' => 'Incendi boschivi in atto',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_non_boschivo',
                'label' => 'Incendi non boschivi',
                'contentOptions' => ['style'=>'width: 60px;']
            ] 
        ];
        
        $cols = [

            [
                'attribute' => 'regione',
                'label' => 'Regione',
                'contentOptions' => ['style'=>'width: 80px;'],
            ],
            [
                'attribute' => 'provincia',
                'label' => 'Provincia',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'num_boschivo',
                'label' => 'Incendi boschivi',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'solo_regionali',
                'label' => 'Incendi boschivi con soli aeromobili regionali',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo_chiuso',
                'label' => 'Incendi boschivi spenti',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo_aperto',
                'label' => 'Incendi boschivi in atto',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_non_boschivo',
                'label' => 'Incendi non boschivi',
                'contentOptions' => ['style'=>'width: 60px;']
            ]            
        ];

        echo $this->render('_export_pdf', [
            'cols' => $cols
        ]);

        ?>

       <?php echo GridView::widget([
        'id' => 'report-eventi',
        'dataProvider' => $dataProvider,
        'responsive'=>false,
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
        'export'=> false,
        'columns' => $cols
    ]); ?>
</div>
