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
        'action' => [ 'report' ],
        'method' => 'get',
    ]); ?>

    
    <?= $form->field($model, 'data_dal', ['options' => ['class' => 'col-lg-4']])->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Data dal'],
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
            'todayHighlight' => true,
            'autoclose'=>true
        ]
    ]); ?>

    <?= $form->field($model, 'data_al', ['options' => ['class' => 'col-lg-4']])->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Data al'],
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
            'todayHighlight' => true,
            'autoclose'=>true
        ]
    ]); ?>

    <div class="form-group col-lg-4">
        <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Annulla', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
