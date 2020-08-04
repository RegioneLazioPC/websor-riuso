<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VolTipoOrganizzazione */

$this->title = 'Aggiorna tipo organizzazione: '.$model->tipologia;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Organizzazione', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vol-tipo-organizzazione-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
