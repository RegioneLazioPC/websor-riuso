<?php

use common\components\FilteredActions;
use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\models\EvtSottostatoEvento;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Eventi calamitosi in corso';
$this->params['breadcrumbs'][] = $this->title;

// Imposta il refresh automatico della grid ogni minuto
$js = "$(document).ready(function() {
            setInterval( function refresh() {
               $.pjax.reload({ container:'#lista-eventi-pjax', timeout:60000 })
            },60000);
      });
";

$this->registerJs($js, $this::POS_READY);

?>

<?= $this->render('_partial_elicotteri_volo', []); ?>

<div class="utl-evento-index">

    <p>
        <?php if (Yii::$app->user->can('createEvento')) : ?>
            <?php $createButton =  Html::a('<i class="glyphicon glyphicon-plus"></i> Crea Nuovo Evento', ['create'], ['class' => 'btn btn-success']) ?>
        <?php else : ?>
            <?php $createButton =  ""; ?>
        <?php endif; ?>
    </p>

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
            'heading' => '<h2 class="panel-title"><i class="glyphicon glyphicon-globe"></i> ' . Html::encode($this->title) . '</h2>',
            'before' => $createButton . Html::a('<i class="glyphicon glyphicon-repeat"></i> Azzera filtri', ['index'], ['class' => 'btn btn-info m10w']),
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
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandAllTitle' => 'Espandi tutto',
                'collapseTitle' => 'Comprimi tutto',
                'expandIcon' => '<span class="fa fa-caret-down"></span>',
                'collapseIcon' => '<span style="color: green" class="fa fa-caret-up"></span>',
                'value' => function ($model, $key, $index, $column) {
                    return (Yii::$app->user->can('viewEvento')) ? GridView::ROW_COLLAPSED : '';
                },
                'detail' => function ($model, $key, $index, $column) {
                    return (Yii::$app->user->can('viewEvento')) ?
                        Yii::$app->controller->renderPartial('_tasks-expand.php', [
                            'model' => $model
                        ]) : null;
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
                    if ($data->stato != 'Chiuso') {
                        $last = new \DateTime($data->dataora_modifica);
                        $now = new \DateTime();
                        $diff = $last->diff($now)->format("%h");
                        if (intval($diff) > 1) {
                            $icons .= '<span class="fas fa-exclamation-triangle m5w text-danger" title="Da gestire" data-toggle="tooltip"></span>';
                        }
                    }

                    //$caps = $data->getCapMessages()->all();
                    $caps = $data->getCapMessages()->select(new \yii\db\Expression("concat('Messaggi ricevuti: ', string_agg( distinct sender, '\n') ) as n"))->asArray()->one();
                    if ($caps/*count($caps)*/ && $caps['n'] != 'Messaggi ricevuti: ') {
                        $title = $caps['n']; // 'Messaggio CAP associato';
                        //foreach ($caps as $c) {
                        //$title .= $c->identifier . " ";
                        //};
                        $icons .= '<span class="fas fa-handshake m5w " title="' . $title . '" data-toggle="tooltip"></span>';
                    }
                    if (!empty($data->richiesteDos)) {
                        $dos = $data->getRichiesteDos()->asArray()->all();
                        $dos_ok = 0;
                        $dos_ko = 0;
                        $total = 0;
                        foreach ($dos as $r) {
                            $total++;
                            if ($r['edited'] == 1) {
                                if ($r['engaged']) {
                                    $dos_ok++;
                                } else {
                                    $dos_ko++;
                                }
                            }
                        }


                        $title = 'Richieste DOS: ' . $total;
                        $title .= " Accettate: " . $dos_ok;
                        $title .= " Rifiutate: " . $dos_ko;
                        $cl = $dos_ok > 0 ? 'text-success' : 'text-danger';

                        $icons .= '<span class="fas fa-fire-extinguisher m5w ' . $cl . '" title="' . $title . '" data-toggle="tooltip"></span>';
                    }
                    if (!empty($data->richiesteElicottero)) {
                        $eli = $data->getRichiesteElicotteroUndeleted()->asArray()->all();
                        $eli_ok = 0;
                        $eli_ko = 0;
                        $eli_to_approve = 0;
                        $total = 0;
                        foreach ($eli as $r) {
                            $total++;
                            if ($r['edited'] == 1) {
                                if ($r['engaged']) {
                                    $eli_ok++;
                                } else {
                                    $eli_ko++;
                                }
                            } else {
                                $eli_to_approve++;
                            }
                        }

                        $color = 'default';
                        if (($eli_ok > 0 || $eli_ko > 0) && $eli_to_approve > 0) {
                            $color = 'lemon';
                        }

                        if ($eli_to_approve == 0) {
                            if ($eli_ok > 0) {
                                $color = 'success';
                            } elseif ($eli_ko > 0) {
                                $color = 'danger';
                            }
                        }


                        $title = 'Richieste elicottero: ' . $total;
                        $title .= " Accettate: " . $eli_ok;
                        $title .= " Rifiutate: " . $eli_ko;

                        //$cl = ($eli_ok > 0) ? 'text-success' : 'text-danger';
                        $icons .= '<span class="fas fa-helicopter m5w text-' . $color . '" title="' . $title . '" data-toggle="tooltip"></span>';
                    }
                    if (!empty($data->richiesteCanadair)) {
                        $cana = $data->getRichiesteCanadair()->asArray()->all();
                        $cana_ok = 0;
                        $cana_ko = 0;
                        $total = 0;
                        foreach ($cana as $r) {
                            $total++;
                            if ($r['edited'] == 1) {
                                if ($r['engaged']) {
                                    $cana_ok++;
                                } else {
                                    $cana_ko++;
                                }
                            }
                        }

                        $title = 'Richieste Canadair: ' . $total;
                        $title .= " Accettate: " . $cana_ok;
                        $title .= " Rifiutate: " . $cana_ko;

                        $cl = ($cana_ok > 0) ? 'text-success' : 'text-danger';

                        $icons .= '<span class="fas fa-plane m5w ' . $cl . '" title="' . $title . '" data-toggle="tooltip"></span>';
                    }


                    $extras = ArrayHelper::map($data->extras, 'id', 'voce');
                    $result = ArrayHelper::filter($extras, [1, 2, 3, 4]);
                    if (!empty($result)) {
                        $string = implode(", ", array_values($result));
                        $iconFeriti = '<span class="fas fa-users text-red m5w" title="' . addslashes($string) . '" data-toggle="tooltip"></span>';
                    }
                    $croce_rossa = ArrayHelper::filter($extras, [24, 25, 26, 27, 28, 29, 30, 31]);
                    $iconCr = '';
                    if (!empty($croce_rossa)) {
                        $string = implode(", ", array_values($croce_rossa));
                        $iconCr = '<span class="fas fa-plus text-red m5w" style="font-size: 20px;" title="' . addslashes($string) . '"data-toggle="tooltip"></span>';
                    }

                    $icon_drone = '';
                    $mezzi_apr = Yii::$app->db->createCommand("SELECT * FROM view_attivazioni_apr where idevento in (
                        SELECT id FROM utl_evento WHERE id = :ide OR idparent = :ide
                    )", [':ide'=>$data->id])->queryAll();
                    $has_m_apr = false;
                    $n_apr_ok = 0;
                    $n_apr_pending = 0;
                    $n_apr_ko = 0;
                    foreach ($mezzi_apr as $m) {
                        $has_m_apr = true;
                        if ($m['stato'] == 1) {
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
                        } elseif ($n_apr_pending > 0) {
                            $icon_drone .= '<span class="m5w" style="" title="' . addslashes($n_apr_pending) . ' in attesa di conferma o chiuse"data-toggle="tooltip">'.Html::img('@web/images/icons/16/drone_grey.png').'</span>';
                        }/* elseif ($n_apr_ko > 0) {
                            $icon_drone .= '<span class="m5w" style="" title="' . addslashes($n_apr_ko) . ' rifiutato/i" data-toggle="tooltip">'.Html::img('@web/images/icons/16/drone_red.png').'</span>';
                        }*/
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
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {assignToSalaEsterna} {task} {public}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if (Yii::$app->user->can('viewEvento')) {
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio evento'),
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if (Yii::$app->user->can('updateEvento')) {
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica evento'),
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'assignToSalaEsterna' => function ($url, $model) {
                        if (Yii::$app->user->can('updateEvento') && Yii::$app->FilteredActions->type == 'regionale') {
                            $url = ['evento/assegna-sala-operativa-esterna', 'id' => $model->id];
                            return Html::a('<span class="fa fa-link"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Assegna a sala esterna'),
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'task' => function ($url, $model) {
                        if (Yii::$app->user->identity->multipleCan([
                            'createTaskEvento', 'updateTaskEvento', 'createIngaggio', 'updateIngaggio',
                            'createRichiestaCanadair', 'createRichiestaElicottero', 'createRichiestaDos',
                            'updateRichiestaCanadair', 'updateRichiestaElicottero', 'updateRichiestaDos'
                        ])) {
                            return Html::a('<span class="fas fa-cogs"></span>&nbsp;&nbsp;', ['evento/gestione-evento?idEvento=' . $model->id], [
                                'title' => Yii::t('app', 'Gestione evento'),
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'public' => function ($url, $model) {
                        if (Yii::$app->user->can('publicEvento')) {
                            if (empty($model->is_public)) {
                                return Html::a('<span class="fa fa-lock"></span>&nbsp;&nbsp;', ['evento/public-event?id=' . $model->id . '&public=1'], [
                                    'title' => Yii::t('app', 'Pubblica evento'),
                                    'data-toggle' => 'tooltip'
                                ]);
                            } else {
                                return Html::a('<span class="fa fa-globe"></span>&nbsp;&nbsp;', ['evento/public-event?id=' . $model->id . '&public=0'], [
                                    'title' => Yii::t('app', 'Trasforma in evento privato'),
                                    'data-toggle' => 'tooltip',
                                    'data-pjax' => 0
                                ]);
                            }
                        } else {
                            return '';
                        }
                    }
                ],
            ],
        ],
    ]); ?>
</div>