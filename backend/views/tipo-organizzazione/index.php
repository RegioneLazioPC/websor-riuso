<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel common\models\VolTipoOrganizzazioneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipi Organizzazioni';
$this->params['breadcrumbs'][] = $this->title;

$cols = [
    ['class' => 'yii\grid\SerialColumn'],

    'id',
    'tipologia',
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
        'template' => (Yii::$app->user->can('deleteTipoOrganizzazione')) ? '{view} {update} {delete}' : '{view} {update}',
        'buttons' => [
            'view' => function ($url, $model) {
                if(Yii::$app->user->can('viewTipoOrganizzazione')){
                    return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Dettaglio tipo organizzazione'),
                        'data-toggle'=>'tooltip'
                    ]) ;
                }else{
                    return '';
                }
            },
            'update' => function ($url, $model) {
                if(Yii::$app->user->can('updateTipoOrganizzazione')){
                    return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Modifica tipo organizzazione'),
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
<div class="vol-tipo-organizzazione-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createTipoOrganizzazione')) echo Html::a('Crea tipo organizzazione', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=> $heading
        ],
        'columns' => $cols
    ]); ?>
</div>
