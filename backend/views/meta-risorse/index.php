<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use common\models\tabelle\TblTipoRisorsaMeta;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlAttrezzaturaTipoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Meta dati risorse';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-attrezzatura-tipo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'label',
            [
                'attribute' => 'type',
                'filter'=> Html::activeDropDownList($searchModel, 'type', TblTipoRisorsaMeta::filterOptionsType(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($model){
                    return $model->tipo();
                }
            ],
            [
                'attribute' => 'show_in_column',
                'filter'=> Html::activeDropDownList($searchModel, 'show_in_column', TblTipoRisorsaMeta::filterOptionsColumn(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($model){
                    return $model->inColumn();
                }
            ],
            [
                'attribute' => 'extra',
                'label' => 'Opzioni',
                'value' => function($model){
                    return $model->extra();
                }
            ],
            'key',
            'ref_id',
            'id_sync',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteTipoRisorsaMeta')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewTipoRisorsaMeta')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio meta risorsa'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateTipoRisorsaMeta')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica meta risorsa'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
