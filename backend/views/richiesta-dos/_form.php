<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaDos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="richiesta-dos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idevento')->textInput() ?>

    <?= $form->field($model, 'idingaggio')->textInput() ?>

    <?= $form->field($model, 'idoperatore')->textInput() ?>

    <?= $form->field($model, 'idcomunicazione')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
