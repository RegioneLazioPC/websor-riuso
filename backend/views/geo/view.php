<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

$this->title = $model->layer_name;
$this->params['breadcrumbs'][] = ['label' => 'Layers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-canadair-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= (Yii::$app->user->can('DeleteGeoLayer')) ? Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler rimuovere questo elemento?',
                'method' => 'post',
            ],
        ]) : null ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'layer_name',
                'label'=>'Nome strato'
            ],
            [
                'attribute'=>'geometry_type',
                'label'=>'Tipo geometria'
            ],
            [
                'attribute'=>'geometry_column',
                'label'=>'Colonna geometria'
            ],
            [
                'attribute'=>'fields',
                'label'=>'Colonne',
                'format'=>'raw',
                'value'=>function($data) {
                    $dati = [];
                    foreach ($data->fields as $key => $value) {
                        $dati[] = '<b>'.$key.'</b>: '.$value;
                    }

                    return implode("<br />",$dati);
                }
            ],
            [
                'attribute'=>'created_at',
                'format'=>'datetime',
                'label'=>'Creazione'
            ]
        ],
    ]) ?>


    <?php 

    $cols = [];
    foreach ($model->fields as $key => $field) {
        if( $key != $model->geometry_column) $cols[] = [
            'attribute' => $key,
            'contentOptions' => ['style' => 'width: 150px;']
        ];
    }

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'hover' => true,
        'toggleData' => false,
        //'floatHeader' => true,
        'containerOptions' => [
            'class' => 'overflow-table'
        ],
        'panel' => [
            'heading' => '<h2 class="panel-title">Record del layer</h2>',
            
        ],
        'columns' => $cols
    ]); ?>
</div>
