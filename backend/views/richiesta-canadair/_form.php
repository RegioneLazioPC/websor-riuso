<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="richiesta-canadair-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idevento')->textInput() ?>

    <?= $form->field($model, 'idoperatore')->textInput() ?>

    <?= $form->field($model, 'idcomunicazione')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
