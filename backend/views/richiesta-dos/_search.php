<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaDosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="richiesta-dos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idevento') ?>

    <?= $form->field($model, 'idingaggio') ?>

    <?= $form->field($model, 'idoperatore') ?>

    <?= $form->field($model, 'idcomunicazione') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
