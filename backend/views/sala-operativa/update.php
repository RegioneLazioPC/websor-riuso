<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSalaOperativa */

$this->title = 'Modifica Sala Operativa: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Lista Sale Operative', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="utl-sala-operativa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
