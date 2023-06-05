<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use kartik\grid\GridView;

use common\models\UtlEvento;
use common\models\EvtSottostatoEvento;
use common\models\LocProvincia;
use yii\helpers\Url;

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Enti task', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-automezzo-tipo-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateEnteTask')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteEnteTask')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler cancellare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descrizione'
        ],
    ]) ?>


    
    <div class="clear"></div>


    <?= GridView::widget([
        'id' => 'lista-eventi',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'hover' => true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'panel' => [
            'heading' => '<h2 class="panel-title"><i class="glyphicon glyphicon-globe"></i> Lista eventi collegati</h2>'
        ],
        'rowOptions' => function ($model) {
            $class = null;
            switch ($model->stato) {
                case 'Non gestito':
                    $class = ['class' => 'blue-td'];
                    break;
                case 'In gestione':
                    $class = ['class' => 'green-td'];
                    break;
                case 'Chiuso':
                    $class = ['class' => 'closed-td'];
                    break;
            }
            return $class;
        },
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if (Yii::$app->user->can('viewEvento')) {
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', 
                                Url::base() . '/evento/view?id='.$model->id, 
                                [
                                    'title' => Yii::t('app', 'Dettaglio evento'),
                                    'data-toggle' => 'tooltip',
                                    'data-pjax' => 0
                                ]);
                        } else {
                            return '';
                        }
                    }
                ],
            ],
            [
                'attribute' => 'num_protocollo',
                'width' => '60px',
                'format' => 'raw',
                'value' => function ($data) {
                    $ret_str = Html::encode($data->num_protocollo);
                    $fronti = $data->getFronti()->all();
                    if (count($fronti) > 0) :
                        $ret_str .= "<ul style=\"font-size: 11px; padding-left: 20px;\">";

                        foreach ($fronti as $f) {
                            $ret_str .= "<li>" . Html::encode($f->num_protocollo) . "</li>";
                        }

                        $ret_str .= "</ul>";
                    endif;
                    return $ret_str;
                }
            ],
            [
                'label' => 'Tipologia',
                'attribute' => 'tipologia_evento',
                'width' => '190px',
                'filter' => Html::activeDropDownList($searchModel, 'tipologia_evento', \common\models\UtlEvento::getNestedFilterTipologie(), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'format' => 'raw',
                'value' => function ($data) {
                    $ret_str = ($data->tipologia) ? Html::encode($data->tipologia->tipologia) : "";

                    if (!empty($data->sottotipologia)) :
                        $ret_str .= "<br /><span style=\"font-size: 11px; padding-left: 20px;\">" . Html::encode($data->sottotipologia->tipologia) . "</span>";
                    endif;
                    return $ret_str;
                }
            ],
            [
                'label' => 'Stato',
                'attribute' => 'stato',
                'filter' => Html::activeDropDownList($searchModel, 'stato', UtlEvento::getStatoOptions(), ['class' => 'form-control', 'prompt' => 'Tutti gli stati']),
                'value' => function ($data) {
                    return $data->stato;
                }
            ],
            [
                'label' => 'Stato interno',
                'attribute' => 'id_sottostato_evento',
                'filter' => Html::activeDropDownList($searchModel, 'id_sottostato_evento', ArrayHelper::map(EvtSottostatoEvento::find()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti gli stati']),
                'value' => function ($data) {
                    return @$data->sottostato->descrizione;
                }
            ],
            [
                'label' => 'Data creazione',
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
                'value' => function ($data) {
                    return Yii::$app->formatter->asDateTime($data->dataora_evento);
                }
            ],
            [
                'label' => 'Data modifica',
                'attribute' => 'dataora_modifica',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function ($data) {
                    return Yii::$app->formatter->asDateTime($data->dataora_modifica);
                }
            ],
            [
                'label' => 'Comune',
                'visible' => Yii::$app->FilteredActions->showFieldComune,
                'attribute' => 'comune.comune',
                'contentOptions' => ['style' => 'width: 300px;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(
                    \common\models\LocComune::find()
                        ->where([
                            Yii::$app->params['region_filter_operator'],
                            'id_regione',
                            Yii::$app->params['region_filter_id']
                        ])
                        ->orderBy([
                            'comune' => SORT_ASC,
                        ])
                        ->all(),
                    'id',
                    'comune'
                ),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear' => true,
                    ]
                ],
                'value' => function ($data) {
                    if (!empty($data['comune'])) {
                        return $data['comune']['comune'];
                    }
                }
            ],
            [
                'label' => 'Provincia',
                'visible' => Yii::$app->FilteredActions->showFieldProvincia,
                'attribute' => 'comune.provincia',
                'width' => '50px',
                'hAlign' => GridView::ALIGN_CENTER,
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
                        'allowClear' => true,
                    ]
                ],
                'value' => function ($data) {
                    if (!empty($data['comune'])) {
                        return $data['comune']['provincia_sigla'];
                    } else {
                        return '';
                    }
                }
            ],
            [
                'label' => 'Indirizzo e località',
                'attribute' => 'indirizzo',
                'value' => function ($data) {
                    if ($data['indirizzo'] != '') {
                        return $data['indirizzo'];
                    } else {
                        return $data['luogo'];
                    }
                }
            ],
            [
                'label' => 'Gestore',
                'attribute' => 'id_gestore_evento',
                'filter' => Html::activeDropDownList($searchModel, 'id_gestore_evento', ArrayHelper::map(\common\models\EvtGestoreEvento::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function ($data) {
                    return @$data->gestore->descrizione;
                }
            ],
            [
                'label' => 'COC',
                'attribute' => 'has_coc',
                'filter' => Html::activeDropDownList($searchModel, 'has_coc', [0 => 'No', 1 => 'Si'], ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function ($data) {
                    return ($data->has_coc == 1) ? 'Si' : 'No';
                }
            ]
        ],
    ]); ?>

    

</div>
