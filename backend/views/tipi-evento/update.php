<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlTipologia */

$this->title = 'Aggiorna tipo evento: '.$model->tipologia;
$this->params['breadcrumbs'][] = ['label' => 'Tipi evento', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tipologia, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-tipologia-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
