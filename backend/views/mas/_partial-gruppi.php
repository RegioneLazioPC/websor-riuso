<?php

use yii\helpers\Html;
use kartik\grid\GridView;

?>

<div class="mass-message-template-index">

    <h3>Seleziona i gruppi a cui inviare il messaggio</h3>
    <?= GridView::widget([
        'id' => 'lista-gruppi',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'before'=> Html::a('<i class="glyphicon glyphicon-repeat"></i> Azzera filtri', ['create-invio', 'id_messaggio'=>$model->id], ['class' => 'btn btn-info m10w']),
            'heading'=> "Elenco gruppi",
        ],
        'columns' => [
            [

                'class' => 'kartik\grid\CheckboxColumn',
                'header' => Html::checkBox('selection_all', false, [
                    'class' => 'group-select-on-check-all'
                ]),
                'checkboxOptions' => function($model, $key, $index, $widget) {
                    return ['value' => $model->id,'class'=>'group-kv-row-checkbox'];
                }

            ],

            'id',
            [
                'attribute'=>'name',
                'label'=>'Nome'
            ]            
        ],
    ]); ?>
</div>