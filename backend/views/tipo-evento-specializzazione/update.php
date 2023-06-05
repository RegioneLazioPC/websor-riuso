<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAggregatoreTipologie */

$this->title = 'Aggiorna connessione: '.Html::encode($model->id);
$this->params['breadcrumbs'][] = ['label' => 'Tipo evento/specializzazione', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="utl-aggregatore-tipologie-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
