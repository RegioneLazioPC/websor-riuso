<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Modifica Organizzazione: ' . $model->denominazione;
$this->params['breadcrumbs'][] = ['label' => 'Lista Organizzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="vol-organizzazione-update">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
