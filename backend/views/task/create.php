<?php

use yii\helpers\Html;


$this->title = 'Aggiungi un ente';
$this->params['breadcrumbs'][] = ['label' => 'Tipi di automezzo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
