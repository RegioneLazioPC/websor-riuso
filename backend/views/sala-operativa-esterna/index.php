<?php

use kartik\grid\GridView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlSalaOperativaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Sale Operative Esterne';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'heading' => '<h2 class="panel-title"><i class="fa fa-building"></i> ' . Html::encode($this->title) . '</h2>',
            'before' => (Yii::$app->user->can('createSalaOperativaEsterna')) ? Html::a('Nuova Sala Operativa', ['create'], ['class' => 'btn btn-success']) : null,
        ],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('listSalaOperativaEsterna')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateSalaOperativaEsterna')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'delete' => function ($url, $model) {
                        if(Yii::$app->user->can('deleteSalaOperativaEsterna')){
                            return Html::a('<span class="fa fa-trash"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Elimina'),
                                'data-toggle'=>'tooltip',
                                'data' => [
                                    'confirm' => "Sicuro di voler rimuovere questo elemento?",
                                    'method' => 'post',
                                    'params' => [
                                        'id' => $model->id
                                    ],
                                ]
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ]
            ],
            'nome',
            'url_endpoint',
        ],
    ]); ?>
</div>