<?php

use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\export\ExportMenu;

//use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Eventi Archiviati';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_partial_elicotteri_volo', []); ?>

<div class="utl-evento-index">

    <?php
    $export_cols = [
        [
            'label' => 'Elicottero',
            'attribute' => 'richiesteMezziAerei',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                return (!empty($data->richiesteElicotteroUndeleted)) ? "x" : "";
            }
        ],
        [
            'label' => 'DOS',
            'attribute' => 'richiesteMezziAerei',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                return (!empty($data->richiesteDos)) ? "x" : "";
            }
        ],
        [
            'label' => 'Canadair',
            'attribute' => 'richiesteMezziAerei',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                return (!empty($data->richiesteCanadair)) ? "x" : "";
            }
        ],
        [
            'label' => 'Persone in pericolo',
            'attribute' => 'richiesteMezziAerei',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                $extras = ArrayHelper::map($data->extras, 'id', 'id');
                $result = ArrayHelper::filter($extras, [1, 2, 3, 4]);
                return (!empty($result)) ? "x" : "";
            }
        ],
        [
            'label' => 'Altre Strutture Attivate',
            'attribute' => 'richiesteMezziAerei',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                $extras = ArrayHelper::map($data->extras, 'id', 'id');
                $result = ArrayHelper::filter($extras, [24, 25, 26, 27, 28, 29, 30, 31]);
                return (!empty($result)) ? "x" : "";
            }
        ],
        [
            'label' => 'Messaggio CAP',
            'attribute' => 'richiesteMezziAerei',
            'format' => 'raw',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                return $data->getCapMessages()->count() > 0 ? 'x' : '';
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
            'value' => function ($data) {
                $ret_str = $data->num_protocollo;
                $f = [];
                $fronti = $data->getFronti()->all();
                if (count($fronti) > 0) :
                    foreach ($fronti as $fr) {
                        $f[] = $fr->num_protocollo;
                    }
                endif;
                return (count($f) > 0) ? $ret_str . ", " . implode(", ", $f) : $ret_str;
            }
        ],
        [
            'label' => 'Tipologia',
            'attribute' => 'tipologia_evento',
            'filter' => Html::activeDropDownList($searchModel, 'tipologia_evento', ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control', 'prompt' => 'Tutti']),
            'value' => function ($data) {
                return ($data->tipologia) ? $data->tipologia->tipologia : "";
            }
        ],

        [
            'label' => 'Sottotipologia',
            'attribute' => 'sottotipologia_evento',
            'value' => function ($data) {
                return (!empty($data->sottotipologia)) ? $data->sottotipologia->tipologia : "";
            }
        ],

        [
            'label' => 'Data apertura',
            'attribute' => 'dataora_evento',
            'value' => function ($data) {
                return Yii::$app->formatter->asDateTime($data->dataora_evento);
            }

        ],
        [
            'label' => 'Data chiusura',
            'attribute' => 'closed_at',
            'value' => function ($data) {
                return Yii::$app->formatter->asDateTime($data->closed_at);
            }

        ],
        [
            'visible' => Yii::$app->FilteredActions->showFieldComune,
            'label' => 'Comune',
            'attribute' => 'comune.comune',
            'value' => function ($data) {
                if (!empty($data['comune'])) {
                    return $data['comune']['comune'];
                }
            }
        ],
        [
            'visible' => Yii::$app->FilteredActions->showFieldProvincia,
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
                    'allowClear' => true,
                ]
            ],
            'value' => function ($data) {
                if (!empty($data['comune'])) {
                    return $data['comune']['provincia']['provincia'] . ' (' . $data['comune']['provincia_sigla'] . ')';
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
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {assign} {task}',
            'buttons' => [
                'view' => function ($url, $model) {
                    if (Yii::$app->user->can('viewEvento')) {
                        return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                            'title' => Yii::t('app', 'Dettaglio evento'),
                            'data-toggle' => 'tooltip'
                        ]);
                    } else {
                        return '';
                    }
                },
                'update' => function ($url, $model) {
                    if (Yii::$app->user->can('openClosedEvento')) {
                        return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                            'title' => Yii::t('app', 'Modifica evento'),
                            'data-toggle' => 'tooltip'
                        ]);
                    } else {
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
            'expandIcon' => '<span class="fa fa-caret-down"></span>',
            'collapseIcon' => '<span style="color: green" class="fa fa-caret-up"></span>',
            'value' => function ($model, $key, $index, $column) {
                return (Yii::$app->user->can('viewEvento')) ? GridView::ROW_COLLAPSED : '';
            },
            'detail' => function ($model, $key, $index, $column) {
                return (Yii::$app->user->can('viewEvento')) ? Yii::$app->controller->renderPartial('_tasks-expand.php', ['model' => $model]) : null;
            },

            'detailOptions' => [
                'class' => 'kv-state-enable',
            ],
        ],

        [
            'label' => 'Info',
            'attribute' => 'richiesteMezziAerei',
            'format' => 'raw',
            'width' => '50px',
            'contentOptions' => ['class' => 'text-center color-gray'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                $icons = '';

                if ($data->getCapMessages()->count() > 0) {
                    $title = 'Messaggio CAP associato';
                    $icons .= '<span class="fas fa-handshake m5w " title="' . $title . '" data-toggle="tooltip"></span>';
                }

                if (!empty($data->richiesteDos)) {
                    $icons .= '<span class="fas fa-fire-extinguisher m5w text-grey" title="Richiesto Dos" data-toggle="tooltip"></span>';
                }
                if (!empty($data->richiesteElicotteroUndeleted)) {
                    $icons .= '<span class="fas fa-helicopter m5w text-grey" title="Richiesto Elicottero" data-toggle="tooltip"></span>';
                }

                if (!empty($data->richiesteCanadair)) {
                    $icons .= '<span class="fas fa-plane m5w text-grey" title="Richiesto Canadair" data-toggle="tooltip"></span>';
                }


                $extras = ArrayHelper::map($data->extras, 'id', 'id');
                $result = ArrayHelper::filter($extras, [1, 2, 3, 4]);
                if (!empty($result)) {
                    $iconFeriti = '<span class="fas fa-users text-grey m5w" title="Presenza persone in pericolo" data-toggle="tooltip"></span>';
                }
                $croce_rossa = ArrayHelper::filter($extras, [24, 25, 26, 27, 28, 29, 30, 31]);
                if (!empty($croce_rossa)) {
                    $iconCr = '<span class="fas fa-plus text-red m5w" style="font-size: 20px;" title="Altre strutture attivate" data-toggle="tooltip"></span>';
                }

                $icon_drone = '';
                    $mezzi_apr = Yii::$app->db->createCommand("SELECT * FROM view_attivazioni_apr where idevento = :ide", [':ide'=>$data->id])->queryAll();
                    $has_m_apr = false;
                    $n_apr_ok = 0;
                    $n_apr_pending = 0;
                    $n_apr_ko = 0;
                foreach ($mezzi_apr as $m) {
                    $has_m_apr = true;
                    if (in_array($m['stato'], [1,3])) {
                        $n_apr_ok++;
                    } elseif ($m['stato'] == 2) {
                        $n_apr_ko++;
                    } else {
                        $n_apr_pending++;
                    }
                }
                if ($has_m_apr) {
                    if ($n_apr_ok > 0) {
                        $icon_drone .= '<span class="m5w" style="" title="' . addslashes($n_apr_ok) . ' confermato/i"data-toggle="tooltip">'.Html::img('@web/images/icons/16/drone_green.png').'</span>';
                    }

                    if ($n_apr_ko > 0) {
                        $icon_drone .= '<span class="m5w" style="" title="' . addslashes($n_apr_ko) . ' rifiutato/i" data-toggle="tooltip">'.Html::img('@web/images/icons/16/drone_red.png').'</span>';
                    }

                    if ($n_apr_pending > 0) {
                        $icon_drone .= '<span class="m5w" style="" title="' . addslashes($n_apr_pending) . ' in attesa di conferma"data-toggle="tooltip">'.Html::img('@web/images/icons/16/drone_grey.png').'</span>';
                    }
                }
                return @$iconCr . @$iconFeriti . @$icons . @$icon_drone;
            }
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
            'label' => 'Data apertura',
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
            'label' => 'Data chiusura',
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
            'value' => function ($data) {
                return Yii::$app->formatter->asDateTime($data->closed_at);
            }

        ],
        [
            'visible' => Yii::$app->FilteredActions->showFieldComune,
            'label' => 'Comune',
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
                        //'char_length(comune)' => SORT_ASC,
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
            'visible' => Yii::$app->FilteredActions->showFieldProvincia,
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
                    'allowClear' => true,
                ]
            ],
            'value' => function ($data) {
                if (!empty($data['comune'])) {
                    return $data['comune']['provincia']['provincia'] . ' (' . $data['comune']['provincia_sigla'] . ')';
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
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {assign} {task}',
            'buttons' => [
                'view' => function ($url, $model) {
                    if (Yii::$app->user->can('viewEvento')) {
                        return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                            'title' => Yii::t('app', 'Dettaglio evento'),
                            'data-toggle' => 'tooltip'
                        ]);
                    } else {
                        return '';
                    }
                },
                'update' => function ($url, $model) {
                    if (Yii::$app->user->can('openClosedEvento')) {
                        return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                            'title' => Yii::t('app', 'Modifica evento'),
                            'data-toggle' => 'tooltip'
                        ]);
                    } else {
                        return '';
                    }
                },

            ],
        ],
    ];

    $heading = "<h2 class=\"panel-title\"><i class=\"fa fa-ban\"></i> " . Html::encode($this->title) . "</h2>";

    if (Yii::$app->user->can('exportData')) {
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
        'responsive' => true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => (Yii::$app->user->can('exportData')) ? false : [],
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'hover' => true,
        'toggleData' => false,
        'panel' => [
            'heading' => $heading
        ],
        'rowOptions' => function ($model) {
            $class = null;
            switch ($model->stato) {
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

</div>