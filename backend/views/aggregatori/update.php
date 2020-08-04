<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAggregatoreTipologie */

$this->title = 'Aggiorna aggregatore: '.Html::encode($model->descrizione);
$this->params['breadcrumbs'][] = ['label' => 'Aggregatori tipologie', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descrizione, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="utl-aggregatore-tipologie-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
