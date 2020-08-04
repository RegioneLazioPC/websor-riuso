<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\AlmAllertaMeteo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="alm-allerta-meteo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php  
    if(!empty($model->data_allerta)) :
        $data_allerta = \DateTime::createFromFormat('Y-m-d', $model->data_allerta);
        if($data_allerta) $model->data_allerta = $data_allerta->format('d-m-Y');
    endif;
    echo $form->field($model, 'data_allerta', ['options' => ['class' => '']])->widget(
    	DatePicker::classname(), [
            'options' => ['placeholder' => 'Data allerta...'],
            'pluginOptions' => [
                'autoclose' => true,
                'language' => 'it',
                'format' => 'dd-mm-yyyy'
            ],
        ]); 
    ?>

    <?= $form->field($model, 'messaggio')->textarea(['rows' => 6])->label('Note') ?>
    
    
    <?php echo $form->field($model, 'mediaFile',[])->widget(
    	FileInput::classname(), [
                ])->label('Inserisci documento pdf'); ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
