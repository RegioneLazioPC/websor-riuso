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

$this->title = 'Report ore di volo';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-index">

        <?php 

        echo $this->render('_search_partial_report', [
            'filter_model' => $filter_model,
            'year' => true,
            'from' => true,
            'to' => true,
        ]);

        $eli_cols = [];
        usort( $elicotteri, function($a, $b){
            return (str_replace(" ", "", $a) > str_replace(" ", "", $b) ) ? 1 : -1;
        });
        foreach ($elicotteri as $key => $value) {
            $eli_cols[] = [
                'attribute' => 'device_'.$key,
                'label' => $value,
                'contentOptions' => ['style'=>'width: 120px;'],
                'value' => function($model) use ($key) {
                    return (isset($model['device_'.$key])) ? $model['device_'.$key] : "";
                }
            ];
        }

        $child_cols = [
            [
                'attribute' => 'giorno',
                'label' => 'Data',
                'contentOptions' => ['style'=>'width: 60px;'],
                'value' => function($model){
                    return $model['giorno'] . "/" . $model['mese'] . "/" . $model['anno'];
                }
            ]
        ];

        $child_cols = array_merge( $child_cols, $eli_cols);

        
        $cols = [

            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandAllTitle' => 'Vedi tutto',
                'collapseTitle' => 'Comprimi tutto',
                'expandIcon'=>'<span class="fa fa-caret-down"></span>',
                'collapseIcon'=>'<span style="color: green" class="fa fa-caret-up"></span>',
                'value' => function ($model, $key, $index, $column) {
                    return (!empty($model['children'])) ? GridView::ROW_COLLAPSED : '';
                },
                'detail'=>function ($model, $key, $index, $column) use ($child_cols) {
                    $childDataProvider = new ArrayDataProvider([
                        'allModels' => $model['children'],
                        'pagination' => false            
                    ]);
                    
                    return GridView::widget([
                        'id' => 'report-figli',
                        'dataProvider' => $childDataProvider,
                        'responsive'=>false,
                        'hover'=>true,
                        'floatHeader' => false,
                        'containerOptions' => [
                            'style'=>'height: 200px; overflow: auto;'
                        ],
                        'floatOverflowContainer' => true,
                        'toggleData'=>false,
                        'export' => Yii::$app->user->can('exportData') ? [] : false,
                        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
                        'panel' => [
                            'heading'=> "Scarica report completo " . ExportMenu::widget([
                            'dataProvider' => $childDataProvider,
                            'columns' => $child_cols,
                            'pjax' => false,
                            'clearBuffers' => true,
                            'target' => ExportMenu::TARGET_BLANK,
                            'onRenderSheet' => function($sheet, $widget) {
                                $sheet->setTitle("Export voli");
                            },
                            'exportConfig' => [
                                    ExportMenu::FORMAT_TEXT => true,
                                    ExportMenu::FORMAT_HTML => false
                                ]                
                            ]),
                            'footer'=>true,
                        ],
                        'pjax'=>false,
                        'export'=> false,
                        'columns' => $child_cols
                    ]);
                },
            ],
            [
                'attribute' => 'anno',
                'label' => 'Anno',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'mese',
                'label' => 'Mese',
                'contentOptions' => ['style'=>'width: 60px;'],
                'value' => function($model){
                    return !empty($model['mese']) ? $model['mese'] : "";
                }
            ]          
        ];

        $cols = array_merge($cols, $eli_cols);

        ?>
        <h4 style="margin-bottom: 18px;">Dati provenienti da ARKA</h4>
       <?php echo GridView::widget([
        'id' => 'report-eventi',
        'dataProvider' => $dataProvider,
        'responsive'=>false,
        'hover'=>true,
        'perfectScrollbar' => false,
        'perfectScrollbarOptions' => [],
        'toggleData'=>false,
        'floatHeader' => true,
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
