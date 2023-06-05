<?php

use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = 'Enti task';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createEnteTask')) echo Html::a('Aggiungi un ente', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteEnteTask')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewEnteTask')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio ente'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateEnteTask')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica ente'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'delete' => function ($url, $model) {
                        if(Yii::$app->user->can('deleteEnteTask')){
                            return Html::a('<span class="fa fa-trash"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Elimina ente'),
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
            'id',
            'descrizione',
            [
                'attribute' => 'code',
                'label' => 'Eventi di impiego',
                'format' => 'raw',
                'value' => function ($data) {
                    $q = "SELECT count(distinct idevento) as all_events, count( distinct idevento ) filter ( where e.stato = 'In gestione') as open_events 
                        from con_operatore_task 
                        left join utl_evento e on e.id = con_operatore_task.idevento
                        where con_operatore_task.idtask = :idtsk";
                    $result = Yii::$app->db->createCommand($q, [':idtsk'=>$data->id])->queryAll();
                    
                    if(count($result) == 0) {
                        return "<b>TOTALI</b>: 0<br /><b>CORRENTI</b>: 0";
                    } else {
                        return "<b>TOTALI</b>: ".$result[0]['all_events']."<br /><b>CORRENTI</b>: ".$result[0]['open_events']."";
                    }
                }
            ]
        ],
    ]); ?>
</div>