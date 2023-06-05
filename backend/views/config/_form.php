<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-config-form">
    <?php if($model->editable) { ?>
    <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>

    <?= $form->errorSummary($model); ?>

    <?php 

    foreach ($avaible_keys as $form_element) {
        switch ($form_element['type']) {
            case 'string':
                echo $form->field($model, $form_element['key'])->textInput();
                break;
            case 'integer':
                echo $form->field($model, $form_element['key'])->textInput(['type' => 'number']);
            break;
            case 'select':

                $opts = [];
                foreach ($form_element['options'] as $opt) {
                    $opts[$opt] = $opt;
                }

                echo $form->field($model, $form_element['key'], ['options'=>[
                    'class'=>'no-p',            
                    ]])->dropDownList($opts, ['prompt' => '']);
            break;
            case 'file':
            echo $form->field($model, $form_element['key'],['options' => ['class'=>'']])->widget(FileInput::classname(), [
                    ]);
            break;
            default:
                echo $form->field($model, $form_element['key'])->textInput();
                break;
        }
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Salva dati', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php } ?>

</div>