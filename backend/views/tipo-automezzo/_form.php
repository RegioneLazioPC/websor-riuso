<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzoTipo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-automezzo-tipo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'is_mezzo_aereo')->checkbox(array(
					'label'=>'',
					'labelOptions'=>array('style'=>'padding:5px;'),
					'disabled'=>false
					))
					->label('Mezzo aereo'); ?>
					
    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
