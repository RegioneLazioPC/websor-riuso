<?php

use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RichiestaCanadairSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Layers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="layers-index">

    
    <p>
        <?= (Yii::$app->user->can('CreateGeoLayer')) ? Html::a('Aggiungi layer', ['create'], ['class' => 'btn btn-success']): null ?>
        <?= (Yii::$app->user->can('CreateGeoLayer')) ? Html::a('Aggiungi layer da tabella precaricata', ['create-from-table'], ['class' => 'btn btn-warning']) : null ?>
    </p>

    <?php 
    $template = '{view}';
    if(Yii::$app->user->can('DeleteGeoLayer')) $template = '{view} {delete}';
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading' => '<h2 class="panel-title">Lista layer</h2>'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
            ],
            'id',
            [
                'attribute' => 'layer_name',
                'label' => 'Nome layer'
            ],
            [
                'attribute' => 'table_name',
                'label' => 'Nome tabella'
            ],
            [
                'attribute' => 'geometry_column',
                'label' => 'Colonna geometria'
            ],
            [
                'attribute' => 'geometry_type',
                'label' => 'Tipo geometria'
            ],
            [
                'attribute'=>'created_at',
                'label'=>'Creazione',
                'format'=>'datetime'
            ]
        ],
    ]); ?>
</div>
