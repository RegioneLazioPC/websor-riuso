<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VolTipoOrganizzazione */

$this->title = 'Aggiorna tipo struttura: '.$model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi di strutture', 'url' => ['tipo-struttura']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->descrizione), 'url' => ['view-tipo-struttura', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="vol-tipo-organizzazione-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-tipo-struttura', [
        'model' => $model,
    ]) ?>

</div>
