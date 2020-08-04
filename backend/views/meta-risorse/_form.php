<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzaturaTipo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meta-risorsa-tipo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?php 
    echo $form->field($model, 'show_in_column', ['options' => ['class'=>'col-lg-12 no-pl']])->widget(
    	Select2::classname(), [
            'data' => [0=>'No', 1=>'Si'],
            'options' => [
                'placeholder' => 'Seleziona'
            ],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ])->label('Mostra in colonna');
   	?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
