<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RubricaGroup */

$this->title = 'Aggiorna gruppo: '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gruppi rubrica', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="rubrica-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
