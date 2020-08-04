<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ConVolontarioIngaggio */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="con-volontario-ingaggio-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_volontario')->textInput() ?>

    <?= $form->field($model, 'id_ingaggio')->textInput() ?>

    <?= $form->field($model, 'refund')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
