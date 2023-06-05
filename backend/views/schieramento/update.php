<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SalaComunaleCap */

$this->title = 'Modifica Schieramento: ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Lista Schieramenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descrizione, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="utl-schieramento-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>