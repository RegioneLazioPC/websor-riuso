<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSpecializzazione */

$this->title = 'Aggiorna specializzazione: '.$model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Specializzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="utl-specializzazione-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
