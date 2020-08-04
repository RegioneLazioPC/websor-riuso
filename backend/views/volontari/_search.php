<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VolVolontarioSearch */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="vol-volontario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'anagrafica.nome') ?>

    <?= $form->field($model, 'anagrafica.cognome') ?>

    <?= $form->field($model, 'ruolo') ?>

    <?= $form->field($model, 'spec_principale') ?>

    <?= $form->field($model, 'valido_dal') ?>

    <?= $form->field($model, 'organizzazione.denominazione') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
