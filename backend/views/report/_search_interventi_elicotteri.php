<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEventoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-evento-search">

    <?php 
    $form = ActiveForm::begin([
        'action' => [ '' ],
        'method' => 'get',
    ]); ?>

    <div class="form-group col-lg-8" style="padding-top: 35px;">
        <?= $form->field($model, 'data_dal', ['options' => ['class' => 'col-lg-6']])->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Data dal'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true,
                'autoclose'=>true
            ]
        ]); ?>

        <?= $form->field($model, 'data_al', ['options' => ['class' => 'col-lg-6']])->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Data al'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true,
                'autoclose'=>true
            ]
        ]); ?>

        <?= $form->field($model, 'n_lanci_da', ['options' => ['class' => 'col-lg-6']])->textInput([])->label('Num. lanci da'); ?>

        <?= $form->field($model, 'n_lanci_a', ['options' => ['class' => 'col-lg-6']])->textInput([])->label('Num. lanci a'); ?>
    </div>

    <div class="form-group col-lg-4" style="padding-top: 70px;">
        <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Annulla', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
