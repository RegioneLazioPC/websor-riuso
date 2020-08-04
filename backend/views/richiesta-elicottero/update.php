<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaElicottero */

$this->title = 'Update Richiesta Elicottero: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Elicotteros', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="richiesta-elicottero-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
