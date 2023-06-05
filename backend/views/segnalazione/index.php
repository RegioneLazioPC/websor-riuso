<?php

use common\models\LocProvincia;
use common\models\UtlSegnalazione;
use common\models\UtlTipologia;
use common\models\UtlRuoloSegnalatore;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use kartik\export\ExportMenu;
//use yii\grid\GridView;
use yii\helpers\Url;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlSegnalazioneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Segnalazioni Emergenze';
$this->params['breadcrumbs'][] = $this->title;

// Imposta il refresh automatico della grid ogni minuto
$js = "$(document).ready(function() {
            setInterval( function refresh() {
               $.pjax.reload({ container:'#lista-segnalazioni-pjax', timeout:60000 })
            },60000);
      });

      $(\"#lista-segnalazioni-pjax\").on(\"pjax:end\", function() {
           jQuery(\"#utlsegnalazionesearch-data_dal-kvdate\").kvDatepicker({
                language: \"it\",
                format: \"dd-mm-yyyy\",
                todayHighlight: true,
                autoclose: true
            })
           jQuery(\"#utlsegnalazionesearch-data_al-kvdate\").kvDatepicker({
                language: \"it\",
                format: \"dd-mm-yyyy\",
                todayHighlight: true,
                autoclose: true
            })
       });
";


$this->registerJs($js, $this::POS_READY);
$this->registerJs($js, $this::POS_READY);








$cols = [
    [
        'attribute' => 'num_protocollo',
        'label' => 'N.Protocollo',
        'headerOptions' => ['style' => 'width:40px']
    ],
    [
        'label' => 'Evento',
        'attribute' => 'evento.num_protocollo',
        'format' => 'raw',
        'value' => function ($model) {
            if (!empty($model->evento)) {
                return $model->evento->num_protocollo;
            } else {
                return '';
            }
        }
    ],
    [
        'label' => 'Segnalatore',
        'attribute' => 'utente',
        'format' => 'raw',
        'filter' => Html::activeDropDownList($searchModel, 'utente', array(1 => 'Cittadino privato', 2 => 'Ente Pubblico', 3 => 'Organizzazione di Volontariato'), ['class' => 'form-control', 'prompt' => 'Tutti']),
        'contentOptions' => ['style' => 'max-width: 200px; white-space: unset;'],
        'value' => function ($data) {

            $tel = (!empty($data->telefono_segnalatore)) ? "<i class=\"fa fa-phone\"></i> " . $data->telefono_segnalatore : "";

            $nome = @$data->nome_segnalatore . " " . @$data->cognome_segnalatore;
            $profilo = '-';
            switch (@$data->utente->tipo) {
                case 2:
                    $profilo = 'Ente pubblico';
                    break;
                case 3:
                    if (!empty($data->organizzazione)) {
                        $profilo = "Organizzazione di volontariato<br />" . Html::a($data->organizzazione->denominazione, ['organizzazione-volontariato/view', 'id' => $data->organizzazione->id], ['class' => '']);
                    } else {
                        $profilo = "Organizzazione di volontariato";
                    }
                    break;
                case null:
                    $profilo = '-';
                    break;
                default:
                    $profilo = 'Cittadino privato';
                    break;
            }

            return $profilo . "<br />" . $nome . "<br />" . $tel;
        }
    ],
    [
        'attribute' => 'fonte',
        'label' => 'Fonte'
    ],
    [
        'label' => 'Ruolo segnalatore',
        'attribute' => 'ruolo_segnalatore',
        'filter' => Html::activeDropDownList($searchModel, 'ruolo_segnalatore', ArrayHelper::map(UtlRuoloSegnalatore::find()->orderBy(['descrizione' => SORT_ASC])->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
        'value' => function ($data) {
            if (!empty($data->utente->tipo) && $data->utente->tipo == 2) {
                return @$data->utente->ruoloSegnalatore->descrizione;
            }
        }
    ],
    [
        'label' => 'Tipologia',
        'attribute' => 'tipologia_evento',
        'format' => 'raw',
        'hAlign' => GridView::ALIGN_CENTER,
        'filter' => Html::activeDropDownList($searchModel, 'tipologia_evento', ArrayHelper::map(UtlTipologia::find()->where('idparent is null')->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control', 'prompt' => 'Tutte le tipologie']),
        'value' => function ($data) {
            if (!empty($data->tipologia->tipologia)) {
                return ($data->tipologia) ? Html::encode($data->tipologia->tipologia) : "";
            }
            if (!empty($data->sos)) {
                return "<i class='fa fa-exclamation-triangle fa-pulse color-red'></i><br/>SOS";
            }
        }
    ],
    [
        'label' => 'Dettagli',
        'attribute' => 'extras',
        'value' => function ($data) {
            $extras = [];
            foreach ($data->extras as $index => $extra) {
                if (in_array($extra->id, [1, 2, 3, 4])) {
                    $extras[] = Html::encode($extra->voce);
                }
            }
            $extrasString = implode('<br>', $extras);
            return $extrasString;
        }
    ],
    [
        'visible' => Yii::$app->FilteredActions->showFieldComune,
        'label' => 'Comune',
        'attribute' => 'comune.comune',
        'headerOptions' => ['style' => 'width:30px'],
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
                return (isset($data['comune']) && !empty($data['comune'])) ? $data['comune']['provincia']['sigla'] : "";
            }
        }
    ],

    [
        'label' => 'Indirizzo e località',
        'attribute' => 'indirizzo',
        'contentOptions' => ['style' => 'max-width: 200px; white-space: normal; word-wrap: break-word;'],
        'value' => function ($data) {
            if ($data['indirizzo'] != '') {
                return $data['indirizzo'];
            } else {
                return $data['luogo'];
            }
        }
    ],
    [
        'label' => 'Stato',
        'attribute' => 'stato',
        'format' => 'html',
        'filter' => Html::activeDropDownList(
            $searchModel,
            'stato',
            UtlSegnalazione::getStatoArray('Verificata e trasformata in evento'),
            ['class' => 'form-control', 'prompt' => 'Tutte le segnalazioni']
        ),

        'value' => function ($data) {
            return $data->stato;
        },
        'contentOptions' => ['style' => 'max-width: 150px; white-space: unset;'],
    ],
    [
        'label' => 'Data',
        'attribute' => 'dataora_segnalazione',
        'format' => 'raw',
        'value' => function ($data) {
            $date = explode(' ', Yii::$app->formatter->asDatetime($data->dataora_segnalazione));
            return $date[0] . '<br>' . $date[1];
        },
        'contentOptions' => ['style' => 'width:50px;'],
    ],
    [
        'label' => 'Note',
        'attribute' => 'note'
    ],
    [
        'label' => 'Operatori',
        'format' => 'html',
        'value' => function ($data) {
            return implode(", ", $data->getOperatori());
        },
    ]
];





?>

<?= $this->render('../evento/_partial_elicotteri_volo', []); ?>

<div class="utl-segnalazione-index">




    <?php if (Yii::$app->user->can('createSegnalazione')) : ?>
        <?php $createButton =  Html::a('Crea nuova segnalazione', ['create'], ['class' => 'btn btn-success']) ?>
    <?php else : ?>
        <?php $createButton =  ""; ?>
    <?php endif; ?>


    <?= GridView::widget([
        'id' => 'lista-segnalazioni',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'hover' => true,
        'pjax' => true,
        'toggleData' => false,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'panel' => [
            'heading' => "Scarica segnalazioni " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]
            ]),
            'before' => '<div style="min-height: 100px;">' . $this->render('_search_partial_segnalazione', [
                'model' => $searchModel,
                'action' => 'index',
                'view' => 'view'
            ]) . '</div><div>' . $createButton . '</div>'
        ],
        'pager' => [
            'firstPageLabel' => 'Pagina iniziale',
            'lastPageLabel'  => 'Pagina finale'
        ],
        'rowOptions' => function ($model) {
            $class = null;
            if (!empty($model->sos)) {
                $class = ['class' => 'orange-td'];
            }
            return $class;
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '#',
                'format' => 'html',
                'value' => function ($data) {
                    if ($data->stato == 'Nuova in lavorazione') {
                        return ($data->fonte != 'App') ? "<i class='fa fa-bell fa-pulse color-red'></i> " : "<i class='fa fa-phone fa-pulse color-red'></i> ";
                    } else {
                        return '';
                    }
                },
            ],
            [
                'attribute' => 'num_protocollo',
                'label' => 'N.Protocollo',
                'headerOptions' => ['style' => 'width:40px']
            ],
            [
                'label' => 'Evento',
                'attribute' => 'evento.num_protocollo',
                'format' => 'raw',
                'value' => function ($model) {
                    if (!empty($model->evento)) {
                        return Html::a($model->evento->num_protocollo, ['evento/view', 'id' => $model->evento->id], ['class' => '', 'target' => '_blank']);
                    } else {
                        return '';
                    }
                }
            ],
            [
                'label' => 'Segnalatore',
                'attribute' => 'utente',
                'format' => 'raw',
                //'filter' => array(1=>'Cittadino privato', 2=>'Ente Pubblico'),
                'filter' => Html::activeDropDownList($searchModel, 'utente', array(1 => 'Cittadino privato', 2 => 'Ente Pubblico', 3 => 'Organizzazione di Volontariato'), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function ($data) {
                    //if(!empty($data->utente->tipo)){
                    $tel = (!empty($data->telefono_segnalatore)) ? "<i class=\"fa fa-phone\"></i> " . $data->telefono_segnalatore : "";

                    $nome = @$data->nome_segnalatore . " " . @$data->cognome_segnalatore;
                    $profilo = '-';
                    switch (@$data->utente->tipo) {
                        case 2:
                            $profilo = 'Ente pubblico';
                            break;
                        case 3:
                            if (!empty($data->organizzazione)) {
                                $profilo = "Organizzazione di volontariato<br />" . Html::a($data->organizzazione->denominazione, ['organizzazione-volontariato/view', 'id' => $data->organizzazione->id], ['class' => '']);
                            } else {
                                $profilo = "Organizzazione di volontariato";
                            }
                            break;
                        case null:
                            $profilo = '-';
                            break;
                        default:
                            $profilo = 'Cittadino privato';
                            break;
                    }

                    return $profilo . "<br />" . $nome . "<br />" . $tel;
                    //} 
                }
            ],
            [
                'attribute' => 'fonte',
                'label' => 'Fonte',
                'filter' => Html::activeDropDownList($searchModel, 'fonte', $searchModel->getFonteArray(), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'headerOptions' => ['style' => 'width:60px']
            ],
            [
                'label' => 'Ruolo segnalatore',
                'attribute' => 'ruolo_segnalatore',
                'format' => 'raw',
                //'filter' => array(1=>'Cittadino privato', 2=>'Ente Pubblico'),
                'filter' => Html::activeDropDownList($searchModel, 'ruolo_segnalatore', ArrayHelper::map(UtlRuoloSegnalatore::find()->orderBy(['descrizione' => SORT_ASC])->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function ($data) {
                    if (!empty($data->utente->tipo) && $data->utente->tipo == 2) {
                        return @$data->utente->ruoloSegnalatore->descrizione;
                    }
                }
            ],
            [
                'label' => 'Tipologia',
                'attribute' => 'tipologia_evento',
                'width' => '190px',
                //'filter'=>ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'),
                'filter' => Html::activeDropDownList($searchModel, 'tipologia_evento', \common\models\UtlEvento::getNestedFilterTipologie(), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'format' => 'raw',
                'value' => function ($data) {
                    if (!empty($data->sos)) {
                        return "<i class='fa fa-exclamation-triangle fa-pulse color-red'></i><br/>SOS";
                    }

                    $ret_str = ($data->tipologia) ? $data->tipologia->tipologia : "";

                    if (!empty($data->sottotipologia)) :
                        $ret_str .= "<br /><span style=\"font-size: 11px; padding-left: 20px;\">" . $data->sottotipologia->tipologia . "</span>";
                    endif;

                    return $ret_str;
                }
            ],
            [
                'label' => 'Dettagli',
                'format' => 'raw',
                'attribute' => 'extras',
                'value' => function ($data) {
                    $extras = [];
                    foreach ($data->extras as $index => $extra) {
                        if (in_array($extra->id, [1, 2, 3, 4])) {
                            $extras[] = $extra->voce;
                        }
                    }
                    $extrasString = implode('<br>', $extras);
                    return $extrasString;
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
                        return (isset($data['comune']) && !empty($data['comune'])) ? $data['comune']['provincia']['sigla'] : "";
                    }
                }
            ],

            [
                'label' => 'Indirizzo e località',
                'attribute' => 'indirizzo',
                //'noWrap' => false,
                //the line below has solved the issue
                'contentOptions' => ['style' => 'max-width: 200px; white-space: normal; word-wrap: break-word;'],
                'value' => function ($data) {
                    if ($data['indirizzo'] != '') {
                        return $data['indirizzo'];
                    } else {
                        return $data['luogo'];
                    }
                }
            ],
            [
                'label' => 'Stato',
                'attribute' => 'stato',
                'format' => 'html',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'stato',
                    UtlSegnalazione::getStatoArray('Verificata e trasformata in evento'),
                    ['class' => 'form-control', 'prompt' => 'Tutte le segnalazioni']
                ),

                'value' => function ($data) {
                    return $data->stato;
                },
                'noWrap' => false,
                'contentOptions' => ['style' => 'max-width:150px; overflow: auto; white-space: normal; word-wrap: break-word;']
            ],
            [
                'label' => 'Data',
                'attribute' => 'dataora_segnalazione',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'format' => 'raw',
                'value' => function ($data) {
                    $date = explode(' ', Yii::$app->formatter->asDatetime($data->dataora_segnalazione));
                    return $date[0] . '<br>' . $date[1];
                },
                'contentOptions' => ['style' => 'width:50px;'],
            ],
            [
                'label' => 'Operatori',
                'format' => 'html',
                'value' => function ($data) {
                    return implode(", ", $data->getOperatori());
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp;&nbsp;{update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if (Yii::$app->user->can('viewSegnalazione')) {
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio segnalazione'),
                                'data-toggle' => 'tooltip'
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if ($model->stato != 'Verificata e trasformata in evento' && Yii::$app->user->can('updateSegnalazione')) {
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica segnalazione'),
                                'data-toggle' => 'tooltip'
                            ]);
                        } else {
                            return '';
                        }
                    },
                ]
            ],
        ],
    ]); ?>
</div>