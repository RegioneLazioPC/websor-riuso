<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzaturaTipo */

$this->title = 'Aggiorna meta risorsa: '.$model->label;
$this->params['breadcrumbs'][] = ['label' => 'Meta risorsa', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-attrezzatura-tipo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
