<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ConVolontarioIngaggio */

$this->title = 'Update Con Volontario Ingaggio: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Con Volontario Ingaggios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="con-volontario-ingaggio-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
