<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaMezzoAereo */

$this->title = 'Update Richiesta Mezzo Aereo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Mezzo Aereos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="richiesta-mezzo-aereo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
