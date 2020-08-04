<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RichiestaMezzoAereoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="richiesta-mezzo-aereo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tipo_intervento') ?>

    <?= $form->field($model, 'priorita_intervento') ?>

    <?= $form->field($model, 'tipo_vegetazione') ?>

    <?= $form->field($model, 'area_bruciata') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
