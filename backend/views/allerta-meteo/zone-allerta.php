<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\AlmZonaAllerta;

use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel common\models\AlmAllertaMeteoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zone di allerta per comune';
$this->params['breadcrumbs'][] = $this->title;


$cols = [
    [   
        'label' => 'Id',
        'attribute' => 'id',
        'width' => '50px',
    ],
    [   
        'label' => 'Comune',
        'attribute' => 'comune',
        'width' => '300px',
    ],
    [   
        'label' => 'Zone di allerta',
        'attribute' => 'zone_allerta',
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(AlmZonaAllerta::find()
            ->all(), 'code', 'code'),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear'=>true,
            ]
        ],
        'value' => function($model) {
            return implode(", ", array_map(function($zona) { return $zona->code; }, @$model->zoneAllerta) );
        }
    ]
];


$heading = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $cols,
        'target' => ExportMenu::TARGET_BLANK,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_HTML => false
        ]                
    ]);
?>
<div class="alm-allerta-meteo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading'=> $heading
        ],
        'columns' => $cols,
    ]); ?>
    <?php Pjax::end(); ?>
</div>
