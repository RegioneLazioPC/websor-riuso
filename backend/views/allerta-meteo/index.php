<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel common\models\AlmAllertaMeteoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Allerte meteo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alm-allerta-meteo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    
    <p>
        <?= Html::a('Crea allerta meteo', ['send-allerta'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            [   
                'label' => 'Id',
                'attribute' => 'id',
                'width' => '50px',
            ],
            [   
                'label' => 'Data allerta',
                'attribute' => 'data_allerta',
                'filter'=> DatePicker::widget([
                    'name'  => 'AlmAllertaMeteoSearch[data_allerta]',
                    'value' => Yii::$app->request->get('AlmAllertaMeteoSearch')['data_allerta'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'language' => 'it',
                        'format' => 'yyyy-mm-dd'
                    ],
                ]),
                'format' => 'date',
                'width' => '250px',
            ],
            [   
                'label' => 'Note',
                'attribute' => 'messaggio',
                'width' => '200px',
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) {
                    return @$model->messages->note;
                }
            ],
            [
                'label' => 'Invio',
                'attribute' =>'uploader',
                'format' => 'raw',
                'value' => function($model) {
                    $str = "";
                    $messages = $model->getMessages()->all();
                    if(count($messages) > 0) {
                        foreach ($messages as $message) {
                            $str .= Html::a('Vedi messaggio #' . $message->id, 
                                ['mas/view', 'id' => $message->id], 
                                ['class' => 'btn btn-success btn-xs', 'style'=>'margin-right: 10px', 'data-pjax'=>0]
                            );
                        }
                    }

                    return $str;
                }
            ],
            [
                'label' => 'Caricato da',
                'attribute' => 'uploader',
                'value' => function($model) {
                    if(!empty($model->file)) {
                        return @$model->file[0]->user->operatore->anagrafica->nome . " " . @$model->file[0]->user->operatore->anagrafica->cognome;
                    }

                    return " ";
                }
            ],
            [
                'label' => 'File',
                'attribute' =>'id_media',
                'format' => 'raw',
                'value' => function($model) {
                    if(!empty($model->file)) {
                        $str = '';
                        foreach ($model->file as $media) {
                            $str .= "<p>" . Html::encode($media->nome) . 
                                 Html::a('Vedi allegato', ['media/view-media', 'id' => $media->id], ['class' => 'btn btn-primary btn-xs', 'style'=>'margin-left: 10px', 'target'=>'_blank', 'data-pjax'=>0]) .
                            "</p>";
                        }
                        return $str;
                    } else {
                        return " - ";
                    }
                    
                }
            ],
            
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {allerta}',
                'buttons' => [
                    'allerta' => function ($url, $model) {
                        if(Yii::$app->user->can('createMassMessage')){
                            return Html::a('<span style="margin-left: 10px" class="glyphicon glyphicon-alert"></span>&nbsp;&nbsp;', ['mass/create', 'id_allerta' => $model->id], [
                                'title' => Yii::t('app', 'Crea messaggio di allerta'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
