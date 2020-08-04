<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-ingaggio-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idevento')->textInput() ?>

    <?= $form->field($model, 'idorganizzazione')->textInput() ?>

    <?= $form->field($model, 'idsede')->textInput() ?>

    <?= $form->field($model, 'idautomezzo')->textInput() ?>

    <?= $form->field($model, 'idattrezzatura')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'stato', ['options'=>[
            'class'=>'no-p',            
            ]])->dropDownList([ 
            '0' => 'Non confermato', 
            '1' => 'Confermato', 
            '2' => 'Rifiutato', 
            '3' => 'Chiuso'
            ], ['prompt' => '']) ?>
            

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
