<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */

$this->title = 'Update Richiesta Canadair: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Canadairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="richiesta-canadair-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
