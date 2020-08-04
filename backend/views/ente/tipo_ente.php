<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipi di enti';
$this->params['breadcrumbs'][] = $this->title;

$cols = [
    ['class' => 'yii\grid\SerialColumn'],
    'id',
    'descrizione',
    'id_sync',
    [
        'label' => 'Aggiornamento zone',
        'attribute' => 'update_zona_allerta_strategy',
        'value' => function($data) {
            return \common\models\ZonaAllertaStrategy::getStrategyLabel( $data['update_zona_allerta_strategy'] );
        }
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view-tipo-ente} {update-tipo-ente}',
        'buttons' => [
            'view-tipo-ente' => function ($url, $model) {
                if(Yii::$app->user->can('Admin')){
                    return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Dettaglio tipo ente'),
                        'data-toggle'=>'tooltip'
                    ]) ;
                }else{
                    return '';
                }
            },
            'update-tipo-ente' => function ($url, $model) {
                if(Yii::$app->user->can('Admin')){
                    return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Modifica tipo ente'),
                        'data-toggle'=>'tooltip'
                    ]) ;
                }else{
                    return '';
                }
            }
        ]
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
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading'=> $heading
        ],
        'columns' => $cols
    ]); ?>
</div>
