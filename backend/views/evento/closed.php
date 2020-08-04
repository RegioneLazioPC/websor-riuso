<?php

use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal;
//use yii\grid\GridView;




$js = "

$(document).on('click', '.archiviaEventi', function(e) { 
    e.preventDefault();
    $('#modal-arc-ev').modal('show');
});

";

$this->registerJs($js, $this::POS_READY);




$this->title = 'Lista Eventi Chiusi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-evento-index">

    <?php 
    $export_cols = [
            [
                'label' => 'Elicottero',
                'attribute' => 'richiesteMezziAerei',
                'width' => '50px',
                'contentOptions' => ['class' => 'text-center color-gray'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($data){
                    return (!empty($data->richiesteElicottero)) ? "x" : "";
                }
            ],
            [
                'label' => 'DOS',
                'attribute' => 'richiesteMezziAerei',
                'width' => '50px',
                'contentOptions' => ['class' => 'text-center color-gray'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($data){
                    return (!empty($data->richiesteDos)) ? "x" : "";
                }
            ],
            [
                'label' => 'Canadair',
                'attribute' => 'richiesteMezziAerei',
                'width' => '50px',
                'contentOptions' => ['class' => 'text-center color-gray'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($data){
                    return (!empty($data->richiesteCanadair)) ? "x" : "";
                }
            ],
            [
                'label' => 'Persone in pericolo',
                'attribute' => 'richiesteMezziAerei',
                'width' => '50px',
                'contentOptions' => ['class' => 'text-center color-gray'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($data){
                    $extras = ArrayHelper::map($data->extras, 'id', 'id');
                    $result = ArrayHelper::filter($extras, [1,2,3,4]);
                    return (!empty($result)) ? "x" : "";
                }
            ],
            [
                'label' => 'Altre Strutture Attivate',
                'attribute' => 'richiesteMezziAerei',
                'width' => '50px',
                'contentOptions' => ['class' => 'text-center color-gray'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($data){
                    $extras = ArrayHelper::map($data->extras, 'id', 'id');
                    $result = ArrayHelper::filter($extras, [24,25,26,27,28,29,30,31]);
                    return (!empty($result)) ? "x" : "";
                }
            ],
            [
                'attribute' => 'lat',
                'width' => '60px',
            ],
            [
                'attribute' => 'lon',
                'width' => '60px',
            ],
            [
                'attribute' => 'num_protocollo',
                'width' => '60px',
                'value' => function($data){
                    $ret_str = $data->num_protocollo;
                    $f = [];
                    $fronti = $data->getFronti()->all();
                    if(count($fronti) > 0) :
                        
                        foreach ($fronti as $fr) {
                           $f[] = $fr->num_protocollo;
                        }
                    endif;
                    return (count($f) > 0) ? $ret_str . ", " . implode(", ",$f) : $ret_str;
                }
            ],
            [   'label' => 'Tipologia',
                'attribute' => 'tipologia_evento',
                'value' => function($data){
                    return ($data->tipologia) ? $data->tipologia->tipologia : "";
                }
            ],
            [   'label' => 'Sottotipologia',
                'attribute' => 'sottotipologia_evento',
                'value' => function($data){
                    return (!empty($data->sottotipologia)) ? $data->sottotipologia->tipologia : "";
                }
            ],

            [   'label' => 'Data apertura',
                'attribute' => 'dataora_evento',
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->dataora_evento);
                }

            ],
            [   'label' => 'Data chiusura',
                'attribute' => 'closed_at',
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->closed_at);
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
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(LocProvincia::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->all(), 'id', 'sigla'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['provincia']['provincia'].' ('.$data['comune']['provincia_sigla'].')';
                    }
                }
            ],
            [
                'label' => 'Indirizzo e località',
                'attribute' => 'indirizzo',
                'value' => function($data){
                    if( $data['indirizzo'] != '') {
                        return $data['indirizzo'];
                    } else {
                        return $data['luogo'];
                    }
                }
            ],
            [
                'label' => 'Gestore',
                'attribute' => 'id_gestore_evento',
                'filter'=> Html::activeDropDownList($searchModel, 'id_gestore_evento', ArrayHelper::map(\common\models\EvtGestoreEvento::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return @$data->gestore->descrizione;
                }
            ],
            [
                'label' => 'COC',
                'attribute' => 'has_coc',
                'filter'=> Html::activeDropDownList($searchModel, 'has_coc', [0=>'No',1=>'Si'], ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->has_coc == 1) ? 'Si' : 'No';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {assign} {task}',
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
                        if(Yii::$app->user->can('openClosedEvento')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica evento'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },

                ],
            ],
        ];
    

    $cols = [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandAllTitle' => 'Espandi tutto',
                'collapseTitle' => 'Comprimi tutto',
                'expandIcon'=>'<span class="fa fa-caret-down"></span>',
                'collapseIcon'=>'<span style="color: green" class="fa fa-caret-up"></span>',
                'value' => function ($model, $key, $index, $column) {
                    return (Yii::$app->user->can('viewEvento')) ? GridView::ROW_COLLAPSED : '';
                },
                'detail'=>function ($model, $key, $index, $column) {
                    return (Yii::$app->user->can('viewEvento')) ? Yii::$app->controller->renderPartial('_tasks-expand.php', ['model'=>$model]) : null;
                },

                'detailOptions'=>[
                    'class'=> 'kv-state-enable',
                ],
            ],

            [
                'label' => 'Info',
                'attribute' => 'richiesteMezziAerei',
                'format' => 'raw',
                'width' => '50px',
                'contentOptions' => ['class' => 'text-center color-gray'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($data){
                    $icons = '';
                    if(!empty($data->richiesteDos)){
                        $icons .= '<span class="fas fa-fire-extinguisher m5w text-grey" title="Richiesto Dos" data-toggle="tooltip"></span>';
                    }
                    if(!empty($data->richiesteElicottero)){
                        $icons .= '<span class="fas fa-helicopter m5w text-grey" title="Richiesto Elicottero" data-toggle="tooltip"></span>';
                    }
                    
                    if(!empty($data->richiesteCanadair)){
                        $icons .= '<span class="fas fa-plane m5w text-grey" title="Richiesto Canadair" data-toggle="tooltip"></span>';
                    }


                    $extras = ArrayHelper::map($data->extras, 'id', 'id');
                    $result = ArrayHelper::filter($extras, [1,2,3,4]);
                    if(!empty($result)){
                        $iconFeriti = '<span class="fas fa-users text-grey m5w" title="Presenza persone in pericolo" data-toggle="tooltip"></span>';
                    }
                    $croce_rossa = ArrayHelper::filter($extras, [24,25,26,27,28,29,30,31]);
                    if(!empty($croce_rossa)){
                        $iconCr = '<span class="fas fa-plus text-red m5w" style="font-size: 20px;" title="Altre strutture attivate" data-toggle="tooltip"></span>';
                    }
                    return @$iconCr . @$iconFeriti . @$icons;
                }
            ],
            [
                'attribute' => 'num_protocollo',
                'width' => '60px',
                'format'=>'raw',
                'value' => function($data){
                    $ret_str = Html::encode($data->num_protocollo);
                    $fronti = $data->getFronti()->all();
                    if(count($fronti) > 0) :
                        $ret_str .= "<ul style=\"font-size: 11px; padding-left: 20px;\">";

                        foreach ($fronti as $f) {
                            $ret_str .= "<li>".Html::encode($f->num_protocollo)."</li>";
                        }

                        $ret_str .= "</ul>";
                    endif;
                    return $ret_str;
                }
            ],
            [   'label' => 'Tipologia',
                'attribute' => 'tipologia_evento',
                'width' => '190px',
                'filter'=> Html::activeDropDownList($searchModel, 'tipologia_evento', \common\models\UtlEvento::getNestedFilterTipologie(), ['class' => 'form-control','prompt' => 'Tutti']),
                'format' => 'raw',
                'value' => function($data){
                    $ret_str = ($data->tipologia) ? Html::encode($data->tipologia->tipologia) : "";
                    
                    if( !empty( $data->sottotipologia ) ) :
                        $ret_str .= "<br /><span style=\"font-size: 11px; padding-left: 20px;\">" . Html::encode($data->sottotipologia->tipologia) . "</span>";
                    endif;
                    return $ret_str;
                }
            ],

            [   'label' => 'Data apertura',
                'attribute' => 'dataora_evento',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->dataora_evento);
                }

            ],
            [   'label' => 'Data chiusura',
                'attribute' => 'closed_at',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->closed_at);
                }

            ],
            [
                'label' => 'Comune',
                'attribute' => 'comune.comune',
                'contentOptions' => ['style'=>'width: 300px;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(
                    \common\models\LocComune::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->orderBy([
                        'comune'=>SORT_ASC, 
                    ])
                    ->all(), 'id', 'comune'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['comune'];
                    }
                }
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'comune.provincia',
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(LocProvincia::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->all(), 'id', 'sigla'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['provincia']['provincia'].' ('.$data['comune']['provincia_sigla'].')';
                    }
                }
            ],
            [
                'label' => 'Indirizzo e località',
                'attribute' => 'indirizzo',
                'value' => function($data){
                    if( $data['indirizzo'] != '') {
                        return $data['indirizzo'];
                    } else {
                        return $data['luogo'];
                    }
                }
            ],
            [
                'label' => 'Gestore',
                'attribute' => 'id_gestore_evento',
                'filter'=> Html::activeDropDownList($searchModel, 'id_gestore_evento', ArrayHelper::map(\common\models\EvtGestoreEvento::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return @$data->gestore->descrizione;
                }
            ],
            [
                'label' => 'COC',
                'attribute' => 'has_coc',
                'filter'=> Html::activeDropDownList($searchModel, 'has_coc', [0=>'No',1=>'Si'], ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->has_coc == 1) ? 'Si' : 'No';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {assign} {task}',
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
                        if(Yii::$app->user->can('openClosedEvento')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica evento'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },

                ],
            ],
        ];
    
    if(Yii::$app->user->can('sendRichiestaElicotteroToCOAU')) echo Html::button(
            'Archivia eventi',
            [
                'title' => Yii::t('app', 'Archivia eventi'),
                'class' => 'archiviaEventi btn btn-info',
                'style' => 'margin-bottom: 12px'
            ]
        );



    $heading = "<h2 class=\"panel-title\"><i class=\"fa fa-ban\"></i> ".Html::encode($this->title)."</h2>";

    if(Yii::$app->user->can('exportData')) {
        $heading .= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $export_cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]                
            ]);
    }
    ?>

    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => (Yii::$app->user->can('exportData')) ? false : [],
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'hover'=>true,
        'toggleData'=>false,
        'panel' => [
            'heading'=> $heading
        ],
        'rowOptions'=>function($model){
            $class = null;
            switch($model->stato){
                case 'Non gestito':
                    $class = ['class' => 'yellow-td'];
                    break;
                case 'In gestione':
                    $class = ['class' => 'orange-td'];
                    break;
                case 'Chiuso':
                    $class = ['class' => 'active'];
                    break;

            }
            return $class;
        },
        'columns' => $cols
    ]); ?>


<?php
if(Yii::$app->user->can('closeEvento')) : 
    Modal::begin([
        'id' => 'modal-arc-ev',
        'header' => '<h2>Archivia gli eventi chiusi</h2>',
        'size' => 'modal-lg'
    ]);

    echo Yii::$app->controller->renderPartial('_form_archive_events', []);
    Modal::end();
endif;
?>


</div>
