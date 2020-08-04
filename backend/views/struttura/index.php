<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;

use kartik\export\ExportMenu;
use common\models\AlmZonaAllerta;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Strutture';
$this->params['breadcrumbs'][] = $this->title;

$cols = [
    ['class' => 'yii\grid\SerialColumn'],
    'id',
    'denominazione',
    'id_sync',
    [
        'label' => 'Zone di allerta',
        'attribute' => 'zone_allerta',
        'width' => '250px',
        'contentOptions' => ['style'=>'max-width: 250px; white-space: unset;'],
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(AlmZonaAllerta::find()
            ->all(), 'code', 'code'),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'multiple' => true,
                'allowClear'=>true,
            ]
        ],
    ],
    [
        'label' => 'Aggiornamento zone',
        'attribute' => 'update_zona_allerta_strategy',
        'filter'=> Html::activeDropDownList(
            $searchModel, 'update_zona_allerta_strategy', \common\models\ZonaAllertaStrategy::getStrategies(), ['class' => 'form-control','prompt' => 'Tutti']),
        'value' => function($data) {
            return \common\models\ZonaAllertaStrategy::getStrategyLabel( $data['update_zona_allerta_strategy'] );
        }
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => Yii::$app->user->can('Admin') ? '{view} {update}' : '{view}'  
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
<div class="utl-ingaggio-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading'=> $heading
        ],
        'columns' => $cols
    ]); ?>
</div>
