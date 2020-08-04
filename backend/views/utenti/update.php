<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlUtente */

$this->title = 'Aggiorna Utente: ' . $model->nome . ' ' . $model->cognome;
$this->params['breadcrumbs'][] = ['label' => 'Lista Utenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-utente-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
