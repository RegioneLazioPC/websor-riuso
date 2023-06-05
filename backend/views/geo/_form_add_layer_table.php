<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'layer_name')->textInput()->label('Nome layer') ?>
    <?= $form->field($model, 'table_name')->textInput()->label('Nome tabella') ?>
    <?= $form->field($model, 'srid')->textInput()->label('SRID * (necessario per una corretta visualizzazione)') ?>

    <div class="form-group">
        <?= Html::submitButton('Conferma', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
