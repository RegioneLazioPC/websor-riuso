<?php


use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$this->title = 'Gestione Evento Num. Protocollo: ' . $evento->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Lista eventi calamitosi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <h2>Operatore: <?php echo Html::encode(@$utente->anagrafica->nome); ?> <?php echo Html::encode(@$utente->anagrafica->cognome); ?></h2>
    <p style="margin-bottom: 18px;"><?php

                                    $address = (!empty($evento->luogo)) ? $evento->luogo : $evento->indirizzo;
                                    if (!empty($evento->comune)) $address .= " (" . $evento->comune->comune . ")";

                                    echo Html::encode($address);
                                    ?>
    </p>

    <?= $this->render('_partial_elicotteri_volo', []); ?>

    <div class="row">
        <div style="margin-bottom: 12px" class="col-lg-6 col-md-6 col-sm-12">
            <b>EFFEMERIDI APPROSSIMATIVE</b>
            <?php
            $dt = new \DateTime;
            $hours = intval($dt->getOffset() / 3600);
            ?>
            <span class="fa fa-sun" style="margin-left: 12px"></span> <?php echo date_sunrise(time(), SUNFUNCS_RET_STRING, $evento->lat, $evento->lon, 90.35, $hours); ?>
            <span class="fa fa-moon" style="margin-left: 12px"></span> <?php echo date_sunset(time(), SUNFUNCS_RET_STRING, $evento->lat, $evento->lon, 90.35, $hours); ?>
        </div>
        <div style="margin-bottom: 12px" class="col-lg-6 col-md-6 col-sm-12">
            <?= $this->render('_partial_geo_query', ['geoQueries'=>$geoQueries, 'position'=>'EVENTO']); ?>
        </div>
    </div>

    <?php if (Yii::$app->user->can('viewEvento')) echo Html::a('Dettagli evento', ['evento/view', 'id' => $evento->id], ['class' => 'btn btn-default', 'style' => 'margin-bottom: 10px']) . '</span>'; ?>


    <?php

    $active_tab = (Yii::$app->request->get('UtlIngaggioSearch')) ? 'ingaggio' : 'default';
    $tab = Yii::$app->request->get('tab');

    $items = [
        [
            'label' => '<span class="fa fa-clipboard"></span> Diario dell\'evento',
            'content' => $this->render('_partial_mattinale', ['model' => $evento, 'tasksSearchModel' => $tasksSearchModel,  'tasksDataProvider' => $tasksDataProvider]),
        ],
        [
            'label' => '<span class="fa fa-bolt"></span> Attivazioni',
            'content' => $this->render('_partial_ingaggi', ['model' => $evento, 'ingaggiSearchModel' => $ingaggiSearchModel, 'ingaggiDataProvider' => $ingaggiDataProvider]),
            'active' => ($active_tab == 'ingaggio') ? true : false
        ],
        [
            'visible' => Yii::$app->FilteredActions->showDos,
            'label' => '<span class="fas fa-fire-extinguisher"></span> Richieste DOS',
            'content' => $this->render('_partial_ric_dos', ['model' => $evento, 'dosSearchModel' => $dosSearchModel, 'dosDataProvider' => $dosDataProvider]),
            'active' => ($tab == 'dos') ? true : false
        ],
        [
            'visible' => Yii::$app->FilteredActions->showElicottero,
            'label' => '<span class="fas fa-helicopter"></span> Richieste Elicottero',
            'content' => $this->render('_partial_ric_elicottero', ['model' => $evento, 'ricElicotteroSearchModel' => $ricElicotteroSearchModel, 'ricElicotteroDataProvider' => $ricElicotteroDataProvider]),
            'active' => ($tab == 'elicottero') ? true : false
        ],
        [
            'visible' => Yii::$app->FilteredActions->showCanadair,
            'label' => '<span class="fa fa-plane"></span> Richieste Canadair',
            'content' => $this->render('_partial_ric_canadair', ['model' => $evento, 'ricCanadairSearchModel' => $ricCanadairSearchModel, 'ricCanadairDataProvider' => $ricCanadairDataProvider]),
            'active' => ($tab == 'canadair') ? true : false
        ],

    ];

    if ($evento->has_coc == 1) {
        $items[] = [
            'label' => '<span class="fa fa-file"></span> Scheda coc',
            'content' => $this->render('_partial_scheda_coc', ['model' => $evento]),
            'active' => ($tab == 'coc') ? true : false
        ];
    }

    if ($evento->getCapMessages()->count() > 0) {
        $items[] = [
            'label' => 'Veicoli da CAP',
            'content' => $this->render('_partial_veicoli_cap', [
                'model' => $evento,
                'hide_btn' => true
            ]),
            'active' => ($tab == 'veicoli_cap') ? true : false
        ];
    }

    $items[] = [
        'label' => '<span class="fa fa-globe"></span> Query Geografiche',
        'content' => "<div style='padding-top: 24px;'>".$this->render('_partial_geo_query', ['geoQueries'=>$geoQueries, 'position'=>'TAB']). "</div>",
        'active' => ($active_tab == 'query_geografiche') ? true : false
    ];

    echo Tabs::widget([
        'encodeLabels' => false,
        'items' => $items
    ]);
    ?>



</div>