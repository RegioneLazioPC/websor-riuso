<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'Aggiorna Segnalazione Emergenza N. Protocollo ' . $model->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Utl Segnalaziones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-segnalazione-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'utente' => $utente,
        'showLatLon' => $showLatLon
    ]) ?>

</div>
