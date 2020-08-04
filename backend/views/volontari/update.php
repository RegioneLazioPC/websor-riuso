<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VolVolontario */

$this->title = 'Aggiorna: '.$model->anagrafica->nome . ' ' . $model->anagrafica->cognome;
$this->params['breadcrumbs'][] = ['label' => 'Volontari', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vol-volontario-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('updateVolontario') && empty($model->id_sync)) echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
