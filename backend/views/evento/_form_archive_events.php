<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\base\DynamicModel;
/* @var $this yii\web\View */
/* @var $model common\models\UtlEventoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-evento-search">
    <p>Inserisci il range di date degli eventi chiusi da archiviare</p>
    <?php 

    $model = new DynamicModel( ['data_dal', 'data_al'] );
    $model
        ->addRule([
            'data_dal', 'data_al'
        ], 'safe')
        ->addRule([
            'data_dal', 'data_al'
        ], 'required');

    $form = ActiveForm::begin([
        'action' => [ 'archive' ],
        'method' => 'post',
    ]); ?>

    <div class="row">
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
    </div>
    <div class="form-group">
        <?= Html::submitButton('Conferma', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="clearfix"></div>
</div>
