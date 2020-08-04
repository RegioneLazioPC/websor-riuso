<?php

use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\MasMessageTemplate;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel common\models\MasMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messaggi';
$this->params['breadcrumbs'][] = $this->title;


$cols = [
            [
                'label'=>'Identificativo',
                'attribute' => 'id',
                'width' => '80px',
            ],
            [
                'label' => 'Titolo',
                'attribute' => 'title'
            ],
            [
                'label' => 'Creato da',
                'attribute' => 'id_user',
                'value' => function($model){
                    return @$model->user->username;
                }
            ],
            [
                'label' => 'File',
                'attribute' =>'id_media',
                'width'=>'250px',
                'format' => 'raw',
                'value' => function($model) {
                    if(!empty($model->file)) {
                        $str = '';
                        foreach ($model->file as $media) {
                            $str .= "<p>" . Html::a('Vedi allegato', ['media/view-media', 'id' => $media->id], ['class' => 'btn btn-primary btn-xs', 'style'=>'margin-left: 10px; color: #fff', 'target'=>'_blank', 'data-pjax'=>0]) . Html::encode($media->nome) . 
                                 
                            "</p>";
                        }
                        return $str;
                    } else {
                        return " - ";
                    }
                }
            ],
            'note',
            [
                'label' => 'Note',
                'attribute' => 'note',
                'contentOptions' => ['style'=>'width: 200px;'],
                'width' => '200px',
            ],
            [
                'label' => 'Data creazione',
                'attribute' => 'created_at',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'format' => 'datetime'
            ],
            [
                'label' => 'Email',
                'attribute' => 'channel_mail',
                'width' => '50px',
                'filter'=> Html::activeDropDownList($searchModel, 'channel_mail', [
                    0 => 'No',
                    1 => 'Si'
                ], ['class' => 'form-control','prompt' => 'seleziona']),
                'value' => function($data){
                    return ($data->channel_mail == 1) ? "Si" : "No";
                }
            ],

            [
                'label' => 'Pec',
                'attribute' => 'channel_pec',
                'width' => '50px',
                'filter'=> Html::activeDropDownList($searchModel, 'channel_pec', [
                    0 => 'No',
                    1 => 'Si'
                ], ['class' => 'form-control','prompt' => 'seleziona']),
                'value' => function($data){
                    return ($data->channel_pec == 1) ? "Si" : "No";
                }
            ],
            [
                'label' => 'Fax',
                'attribute' => 'channel_fax',
                'width' => '50px',
                'filter'=> Html::activeDropDownList($searchModel, 'channel_fax', [
                    0 => 'No',
                    1 => 'Si'
                ], ['class' => 'form-control','prompt' => 'seleziona']),
                'value' => function($data){
                    return ($data->channel_fax == 1) ? "Si" : "No";
                }
            ],
            [
                'label' => 'Sms',
                'attribute' => 'channel_sms',
                'width' => '50px',
                'filter'=> Html::activeDropDownList($searchModel, 'channel_sms', [
                    0 => 'No',
                    1 => 'Si'
                ], ['class' => 'form-control','prompt' => 'seleziona']),
                'value' => function($data){
                    return ($data->channel_sms == 1) ? "Si" : "No";
                }
            ],
            [
                'label' => 'Push',
                'attribute' => 'channel_push',
                'width' => '50px',
                'filter'=> Html::activeDropDownList($searchModel, 'channel_push', [
                    0 => 'No',
                    1 => 'Si'
                ], ['class' => 'form-control','prompt' => 'seleziona']),
                'value' => function($data){
                    return ($data->channel_push == 1) ? "Si" : "No";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('listMasMessage')){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettagli'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateMasMessage')){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ]
        ];


?>
<div class="mass-message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Crea nuovo messaggio', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'panel' => [
            'heading'=> "Scarica messaggi " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]                
            ]),
        ],
        'rowOptions'=>function($model){
            $class = null;
            return ($model->getInvio()->count() > 0) ? 
                ['class' => 'green-td'] :
                ['class' => 'red-td'];
        },
        'columns' => $cols
        
    ]); ?>
</div>
