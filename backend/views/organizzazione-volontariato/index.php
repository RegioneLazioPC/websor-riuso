<?php

use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\helpers\Html;

use common\models\VolTipoOrganizzazione;
use common\models\VolOrganizzazione;

use common\models\LocProvincia;
use common\models\LocComune;
use common\models\AlmZonaAllerta;
use common\models\TblSezioneSpecialistica;
use common\models\VolConvenzione;

/* @var $this yii\web\View */
/* @var $searchModel common\models\VolOrganizzazioneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Organizzazioni di volontariato';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-organizzazione-index">

    <p>
        <?php if (Yii::$app->user->can('createOrganizzazione')) echo Html::a('Crea Nuova Organizzazione', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'hover' => true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'panel' => [
            'heading' => '<h2 class="panel-title"><i class="fa fa-users"></i> ' . Html::encode($this->title) . '</h2>',
        ],
        'columns' => [
            [
                'label' => 'Num. elenco territoriale',
                'attribute' => 'ref_id'
            ],
            [
                'label' => 'Tipo',
                'attribute' => 'id_tipo_organizzazione',
                'filter' => Html::activeDropDownList($searchModel, 'id_tipo_organizzazione', ArrayHelper::map(VolTipoOrganizzazione::find()->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function ($data) {
                    return (isset($data['tipoOrganizzazione'])) ? @$data['tipoOrganizzazione']['tipologia'] : '-';
                }
            ],
            [
                'label' => 'Denominazione',
                'attribute' => 'denominazione',
                'width' => '250px',
                'contentOptions' => ['style' => 'max-width: 250px; white-space: unset;'],
            ],
            [
                'label' => 'Stato',
                'attribute' => 'stato_iscrizione',
                'filter' => Html::activeDropDownList($searchModel, 'stato_iscrizione', [
                    VolOrganizzazione::STATO_ATTIVA => 'Attiva',
                    -1 => 'Non attiva'
                ], ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function ($data) {
                    return $data->getNomeStato();
                }
            ],
            [
                //'visible' => Yii::$app->FilteredActions->showFieldComune,
                'label' => 'Comune',
                'attribute' => 'comune',
                'width' => '200px',
                'contentOptions' => ['style' => 'max-width: 200px; white-space: unset;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(
                    LocComune::find()
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
            ],
            [
                //'visible' => Yii::$app->FilteredActions->showFieldProvincia,
                'label' => 'Provincia',
                'attribute' => 'provincia',
                'width' => '130px',
                'contentOptions' => ['style' => 'max-width: 130px; white-space: unset;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(LocProvincia::find()
                    ->where([
                        Yii::$app->params['region_filter_operator'],
                        'id_regione',
                        Yii::$app->params['region_filter_id']
                    ])
                    ->all(), 'sigla', 'sigla'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label' => 'Sedi',
                'attribute' => 'sedi',
                'format' => 'raw',
                'visible' => Yii::$app->FilteredActions->type == 'comunale' ? true : false,
                'value' => function ($data) {
                    $sede = '<ul>';
                    foreach ($data->volSedes as $key => $valSede) {
                        $sede .= '<li>' . $valSede->tipo . ' : ' . $valSede->indirizzo . ' - ' . $valSede->locComune->comune . ' (' . $valSede->locComune->provincia_sigla . ')</li>';
                    }
                    $sede .= '</ul>';
                    return $sede;
                }
            ],
            [
                'label' => 'Specializzazioni',
                'attribute' => 'sezione_specialistica',
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(TblSezioneSpecialistica::find()
                    ->all(), 'id', 'descrizione'),
                'width' => '250px',
                'contentOptions' => ['style' => 'max-width: 250px; white-space: unset;'],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label' => 'Zone di allerta',
                'attribute' => 'zone_allerta',
                'width' => '250px',
                'contentOptions' => ['style' => 'max-width: 250px; white-space: unset;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(AlmZonaAllerta::find()
                    ->all(), 'code', 'code'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label' => 'Aggiornamento zone',
                'attribute' => 'update_zona_allerta_strategy',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'update_zona_allerta_strategy',
                    \common\models\ZonaAllertaStrategy::getStrategies(),
                    ['class' => 'form-control', 'prompt' => 'Tutti']
                ),
                'value' => function ($data) {
                    return \common\models\ZonaAllertaStrategy::getStrategyLabel($data['update_zona_allerta_strategy']);
                }
            ],
            [
                'label' => 'Convenzione',
                'attribute' => 'convenzione',
                'format' => 'raw',
                'value' => function ($data) {
                    return !empty($data->convenzione) ? '<span class="text-success"><b>Attiva</b></span>' : '<span class="text-danger"><b>Non Attiva</b></span>';
                },
                //'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => [1 => 'Attiva', 2 => 'Non attiva'],
                'visible' => Yii::$app->FilteredActions->type == 'comunale' ? true : false
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'text-align:center'],
                'template' => (Yii::$app->user->can('deleteOrganizzazione')) ? '{view} {update} {delete} {convenzione}' : '{view} {update} {convenzione} ',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if (Yii::$app->user->can('viewOrganizzazione')) {
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio organizzazione'),
                                'data-toggle' => 'tooltip'
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if (Yii::$app->user->can('updateOrganizzazione') && empty($model->id_sync)) {
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica organizzazione'),
                                'data-toggle' => 'tooltip'
                            ]);
                        } else {
                            return '';
                        }
                    },
                    // 'convenzione' => function ($url, $model) {

                    //     // Cehck if exist convenzione and add button to delete
                    //     if (!empty($model->convenzione) && Yii::$app->FilteredActions->type == 'comunale') {

                    //         if (Yii::$app->user->can('viewOrganizzazione')) {
                    //             $url = ['delete-convenzione', 'id' => $model->id];
                    //             return Html::a('<span class="fa fa-trash color-white"></span>&nbsp;&nbsp;Elimina convenzione', $url, [
                    //                 'class' => 'btn btn-sm btn-block btn-danger m5h',
                    //                 'title' => Yii::t('app', 'Elimina convenzione'),
                    //                 'data-toggle' => 'tooltip',
                    //                 'data' => [
                    //                     'confirm' => 'Sei sicuro di voler eliminare questa convenzione?',
                    //                     'method' => 'post',
                    //                 ],
                    //             ]);
                    //         }
                    //     }

                    //     // Cehck if comune is different and show button to add convenzione
                    //     if (Yii::$app->FilteredActions->type == 'comunale' && Yii::$app->user->can('viewOrganizzazione')) {
                    //         $url = ['add-convenzione', 'id' => $model->id];
                    //         return Html::a('<span class="fa fa-plus color-white"></span>&nbsp;&nbsp;Attiva convenzione', $url, [
                    //             'class' => 'btn btn-sm btn-block btn-success m5h',
                    //             'title' => Yii::t('app', 'Aggiungi convenzione'),
                    //             'data-toggle' => 'tooltip',
                    //             'data' => [
                    //                 'confirm' => 'Sei sicuro di voler attivare una convenzione per questa organizzazione?',
                    //                 'method' => 'post',
                    //             ],
                    //         ]);
                    //     }

                    //     return '';
                    // },
                ]
            ],
        ],
    ]); ?>
</div>