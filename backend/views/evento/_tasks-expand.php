<?php
use common\models\UtlEvento;
use common\models\ConOperatoreEvento;
use common\models\ConOperatoreTask;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$query = ConOperatoreTask::find()->joinWith(['operatore', 'task', 'funzioneSupporto', 'evento'])
->where(['or',
    ['idevento' => $model->id],
    ['utl_evento.idparent' => $model->id]
])
->orderBy('dataora DESC');
$dataProvider = new ActiveDataProvider([
    'query' => $query
]);


$query_fronti = UtlEvento::find()->where(['idparent' => $model->id]);
$dataProvider_fronti = new ActiveDataProvider([
    'query' => $query_fronti
]);

?>
<div class="utl-task-gridview">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'panel' => [
                    'heading'=>'<h2 class="panel-title">'.Html::encode('Diario dell\'evento - Lista attività svolte').'</h2>',

                ],
                'columns' => [
                    [
                        'format' => 'raw',
                        'attribute' => 'dataora',
                        'contentOptions' => ['style'=>'max-width: 80px; white-space: normal; overflow: auto; word-wrap: break-word;'],
                        'value' => function($model){
                            return Yii::$app->formatter->asDatetime($model->dataora);
                        }
                    ],
                    [
                        'attribute' => 'operatore',
                        'label' => 'Operatore',
                        'contentOptions' => ['style'=>'max-width: 100px; white-space: normal; overflow: auto; word-wrap: break-word;'],
                        'value' => function($model){
                            return (!empty($model->operatore)) ? Html::encode( @$model->operatore->anagrafica->nome . " " . @$model->operatore->anagrafica->cognome ) : 'CHIUSO DA SCRIPT';
                        }
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'funzioneSupporto.descrizione',
                        'label' => 'Funzione di supporto',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'task.descrizione',
                        'label' => 'Attività operativa',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'note',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                ],
            ]); ?>



    <?php 
    $heading = '<h2 class="panel-title">Fronti</h2>';
    if(Yii::$app->user->can('createEvento')) $heading .= " ".
        Html::a('<i class="glyphicon glyphicon-plus"></i> Crea Nuovo Fronte', ['create', 'idparent'=>$model->id], ['class' => 'btn btn-success']);

    ?>

  
    <?= GridView::widget([
        'id' => 'lista-sotto-eventi',
        'dataProvider' => $dataProvider_fronti,
        'responsive'=>true,
        'hover'=>true,
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'panel' => [
            'heading'=>$heading,
            
        ],
        'rowOptions'=>function($model){
            $class = null;
            switch($model->stato){
                case 'Non gestito':
                    $class = ['class' => 'blue-td'];
                    break;
                case 'In gestione':
                    $class = ['class' => 'green-td'];
                    break;
                case 'Chiuso':
                    $class = ['class' => 'gray-td'];
                    break;

            }
            return $class;
        },
        'columns' => [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandAllTitle' => 'Espandi tutto',
                'collapseTitle' => 'Comprimi tutto',
                'expandIcon'=>'<span class="fa fa-caret-down"></span>',
                'collapseIcon'=>'<span style="color: green" class="fa fa-caret-up"></span>',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail'=>function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_subtasks-expand.php', [
                        'model'=>$model
                    ]);
                },

                'detailOptions'=>[
                    'class'=> 'kv-state-enable',
                ],
            ],
            [
                'attribute' => 'num_protocollo',
                'width' => '60px'
            ],
            [   'label' => 'Effetto principale evento',
                'attribute' => 'tipologia_evento',
                'width' => '190px',
                'value' => function($data){
                    return ($data->tipologia) ? $data->tipologia->tipologia : "";
                }
            ],
            [   'label' => 'Stato',
                'attribute' => 'stato',
                'value' => function($data){
                    return $data->stato;
                }
            ],
            [   'label' => 'Data creazione',
                'attribute' => 'dataora_evento',
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->dataora_evento);
                }
            ],
            [   'label' => 'Data modifica',
                'attribute' => 'dataora_modifica',
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->dataora_modifica);
                }
            ],
            [
                'label' => 'Comune',
                'attribute' => 'comune.comune',
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['comune'];
                    }
                }
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'comune.provincia',
                'width' => '50px',
                'hAlign' => GridView::ALIGN_CENTER,
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['provincia_sigla'];
                    }else{
                        return '';
                    }
                }
            ],
            [
                'label' => 'Indirizzo e località',
                'attribute' => 'indirizzo',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {task}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewEvento')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio evento'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateEvento')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica evento'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'task' => function ($url, $model) {
                        $operatoreId = Yii::$app->user->identity->operatore->id;
                        if(Yii::$app->user->can('createTaskEvento')){
                            return Html::a('<span class="fas fa-cogs"></span>&nbsp;&nbsp;', ['evento/gestione-evento?idEvento='.$model->id], [
                                'title' => Yii::t('app', 'Gestione evento'),
                                'data-toggle'=>'tooltip'
                            ]);
                        }else{
                            return '';
                        }
                    },
                ],
            ],
        ],
    ]); ?>

        </div>

    </div>



</div>
