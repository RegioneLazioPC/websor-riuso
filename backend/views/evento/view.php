<?php

use kartik\grid\GridView;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\UtlEvento;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$t = ($model->idparent) ? "fronte" : "evento";



$this->title = 'Dettaglio ' . $t . ' N. Protocollo ' . $model->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Lista eventi calamitosi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-evento-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_partial_elicotteri_volo', []); ?>

    <div class="row">
        <div style="margin-bottom: 12px" class="col-lg-6 col-md-6 col-sm-12">
            <b>EFFEMERIDI APPROSSIMATIVE</b>
            <?php
            $dt = new \DateTime;
            $hours = intval($dt->getOffset() / 3600);
            ?>
            <span class="fa fa-sun" style="margin-left: 12px"></span> <?php echo date_sunrise(time(), SUNFUNCS_RET_STRING, $model->lat, $model->lon, 90.35, $hours); ?>
            <span class="fa fa-moon" style="margin-left: 12px"></span> <?php echo date_sunset(time(), SUNFUNCS_RET_STRING, $model->lat, $model->lon, 90.35, $hours); ?>
        </div>
        <div style="margin-bottom: 12px" class="col-lg-6 col-md-6 col-sm-12">
            <?= $this->render('_partial_geo_query', ['geoQueries'=>$geoQueries, 'position'=>'EVENTO']); ?>
        </div>
    </div>

    <p>
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>

        <?php
        if (Yii::$app->user->can('updateEvento')) {
            echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
        }
        ?>
        <?php
        if (
            Yii::$app->user->identity->multipleCan([
                'createTaskEvento', 'updateTaskEvento', 'createIngaggio', 'updateIngaggio',
                'createRichiestaCanadair', 'createRichiestaElicottero', 'createRichiestaDos',
                'updateRichiestaCanadair', 'updateRichiestaElicottero', 'updateRichiestaDos'
            ])
            && $model->stato != 'Chiuso'
        ) {
            echo Html::a('Gestione', ['gestione-evento', 'idEvento' => $model->id], ['class' => 'btn btn-success']);
        }
        ?>

        <?php
        if (Yii::$app->user->can('removeEvento')) {
            echo Html::a('Cancella', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Sicuro di voler eliminare questo elemento? Questa azione è irreversibile',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'stato',
                    [
                        'attribute' => 'id_sottostato_evento',
                        'label' => 'Stato interno',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::encode(@$data->sottostato->descrizione);
                        }
                    ],
                    [
                        'label' => 'Tipologia Evento',
                        'attribute' => 'tipologia_evento',
                        'value' => function ($model) {
                            return ($model->tipologia) ? $model->tipologia->tipologia : "";
                        }
                    ],
                    [
                        'label' => 'Sotto Tipologia Evento',
                        'attribute' => 'sottotipologia_evento',
                        'value' => !empty($model->sottotipologia->tipologia) ? $model->sottotipologia->tipologia : 'Non valorizzata'
                    ],
                    [
                        'label' => 'Comune',
                        'attribute' => 'idcomune',
                        'value' => function ($model) {
                            if (isset($model->idcomune)) return $model->comune->comune . " (" . $model->comune->provincia->sigla . ")";
                            return '';
                        }
                    ],
                    'indirizzo:ntext',
                    'luogo:ntext',
                    'lat',
                    'lon',
                    'note:ntext',
                    [
                        'attribute' => 'id_gestore_evento',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::encode(@$data->gestore->descrizione);
                        }
                    ],
                    [
                        'attribute' => 'has_coc',
                        'label' => 'Assegnato al COC',
                        'value' => function ($data) {
                            return ($data->has_coc == 1) ? 'Si' : 'No';
                        }
                    ],
                    [
                        'attribute' => 'dataora_evento',
                        'value' => function ($data) {
                            return Yii::$app->formatter->asDateTime($data->dataora_evento);
                        }
                    ],
                    [
                        'attribute' => 'dataora_modifica',
                        'value' => function ($data) {
                            return Yii::$app->formatter->asDateTime($data->dataora_modifica);
                        }
                    ],
                ],
            ]); ?>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php if (Yii::$app->FilteredActions->showCartografico) : ?>
                <?php echo '<span class="carto-link">' . Html::a('Cartografia', ['/sistema-cartografico?lat=' . $model->lat . '&lon=' . $model->lon], ['class' => 'btn btn-warning', 'style' => 'margin-bottom: 10px']) . '</span>'; ?>
            <?php endif; ?>
            <div id="map-canvas" class="site-index" ng-app="mapAngular">

                <ui-gmap-google-map center='{latitude: <?php echo $model->lat; ?>, longitude: <?php echo $model->lon; ?>}' zoom='10'>

                    <ui-gmap-marker coords="{latitude: <?php echo $model->lat; ?>, longitude: <?php echo $model->lon; ?>}" idkey="<?php echo $model->id; ?>"> </ui-gmap-marker>
                    <?php
                    $sottoeventi = UtlEvento::find()->where(['idparent' => $model->id])->all();
                    foreach ($sottoeventi as $sttevt) {

                        switch ($sttevt->stato) {
                            case 'Allarme':
                                $st = 'allarme';
                                break;
                            case 'Emergenza':
                                $st = 'emergenza';
                                break;
                            default:
                                $st = 'preallarme';
                                break;
                        }
                    ?>
                        <ui-gmap-marker coords="{latitude: <?php echo $sttevt->lat; ?>, longitude: <?php echo $sttevt->lon; ?>}" idkey="<?php echo $sttevt->id; ?>" options="{
                                icon: '<?php echo Yii::$app->request->baseUrl . '/images/map-markers/evento-' . $sttevt->tipologia_evento  . '.png'; ?>'
                            }"> </ui-gmap-marker>
                    <?php
                    }
                    ?>

                </ui-gmap-google-map>

            </div>

        </div>
    </div>

    <?php
    $active_tab = (Yii::$app->request->get('UtlIngaggioSearch')) ? 'attivazione' : 'default';
    $widget_els = [
        [
            'label' => 'Diario dell\'evento',
            'content' => $this->render('_partial_mattinale', ['model' => $model, 'tasksSearchModel' => $tasksSearchModel, 'tasksDataProvider' => $tasksDataProvider]),
            'active' => ($active_tab != 'attivazione') ? true : false
        ]
    ];

    if (!$model->idparent) :
        $widget_els[] = [
            'label' => 'Fronti',
            'content' => $this->render('_view_list_fronti', ['model' => $model]),
        ];
    endif;

    $widget_els[] = [
        'label' => 'Segnalazioni',
        'content' => $this->render('_partial_segnalazioni', ['segnalazioniSearchModel' => $segnalazioniSearchModel, 'segnalazioniDataProvider' => $segnalazioniDataProvider]),
    ];

    $widget_els[] = [
        'label' => 'Attivazioni',
        'content' => $this->render('_partial_ingaggi', ['ingaggiSearchModel' => $ingaggiSearchModel, 'ingaggiDataProvider' => $ingaggiDataProvider, 'model' => $model, 'hide_btn' => true]),
        'active' => ($active_tab == 'attivazione') ? true : false
    ];


    $widget_els[] = [
        'label' => 'Volontari attivati',
        'content' => $this->render('_partial_volontari_attivazioni', [
            'volontariSearchModel' => $volontariSearchModel,
            'volontariDataProvider' => $volontariDataProvider,
            'model' => $model,
            'hide_btn' => true
        ]),
        'active' => ($active_tab == 'volontari') ? true : false
    ];

    if ($model->stato == 'Chiuso') {

        $widget_els = array_merge($widget_els, [
            [
                'label' => 'Richieste DOS',
                'content' => $this->render('_partial_ric_dos', ['model' => $model, 'dosSearchModel' => $dosSearchModel, 'dosDataProvider' => $dosDataProvider]),
                'active' => ($active_tab == 'dos') ? true : false
            ],
            [
                'label' => 'Richieste Elicottero',
                'content' => $this->render('_partial_ric_elicottero', ['model' => $model, 'ricElicotteroSearchModel' => $ricElicotteroSearchModel, 'ricElicotteroDataProvider' => $ricElicotteroDataProvider]),
                'active' => ($active_tab == 'elicottero') ? true : false
            ],
            [
                'label' => 'Richieste Canadair',
                'content' => $this->render('_partial_ric_canadair', ['model' => $model, 'ricCanadairSearchModel' => $ricCanadairSearchModel, 'ricCanadairDataProvider' => $ricCanadairDataProvider]),
                'active' => ($active_tab == 'canadair') ? true : false
            ]
        ]);
    }

    if ($model->getCapMessages()->count() > 0) {
        $widget_els[] = [
            'label' => 'Veicoli da CAP',
            'content' => $this->render('_partial_veicoli_cap', [
                'model' => $model,
                'hide_btn' => true
            ]),
            'active' => ($active_tab == 'veicoli_cap') ? true : false
        ];
    }

    if (!empty($model->getCapMessagesFromReference())) {
        $widget_els[] = [
            'label' => 'Messaggi CAP Esterni',
            'content' => $this->render('_partial_messaggi_cap', [
                'model' => $model,
                'hide_btn' => true
            ]),
            'active' => ($active_tab == 'messaggi_cap_esterni') ? true : false
        ];
    }

    $widget_els[] = [
        'label' => 'Query geografiche',
        'content' => "<div style='padding-top: 24px;'>".$this->render('_partial_geo_query', ['geoQueries'=>$geoQueries, 'position'=>'TAB']). "</div>",
        'active' => ($active_tab == 'query_geografiche') ? true : false
    ];

    echo Tabs::widget([
        'items' => $widget_els,
    ]);

    ?>


</div>