<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
$t = ($model->idparent) ? "fronte" : "evento";

$this->title = 'Aggiorna '.$t.' N. Protocollo ' . $model->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Evento', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'N. Protocollo '.$model->num_protocollo, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="utl-evento-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tipoItems' => $tipoItems,
        'tasksSearchModel' => $tasksSearchModel,
        'tasksDataProvider' => $tasksDataProvider,
        'segnalazioniSearchModel' => $segnalazioniSearchModel,
        'segnalazioniDataProvider' => $segnalazioniDataProvider,
        'ingaggiSearchModel' => $ingaggiSearchModel,
        'ingaggiDataProvider' => $ingaggiDataProvider,
        'showLatLon' => $showLatLon
    ]) ?>

</div>
