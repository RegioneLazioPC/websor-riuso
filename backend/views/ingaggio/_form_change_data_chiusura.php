<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UtlIngaggio;
use kartik\widgets\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */

$dt = \DateTime::createFromFormat('Y-m-d H:i:s', $model->closed_at);
$model->closed_at = $dt->format('d-m-Y H:i');

$dt = \DateTime::createFromFormat('Y-m-d H:i:s', $model->created_at);
$model->created_at = $dt->format('d-m-Y H:i');

?>

<div class="utl-ingaggio-form">

    <?php $form = ActiveForm::begin(); 
    $form->action = ['change-data', 'id'=>$model->id];
    ?>

    <?= $form->field($model, 'created_at', ['options' => ['class' => '']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'data apertura'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy HH:ii',
                'todayHighlight' => true,
                'autoclose'=>true
            ]
        ])->label('Data apertura'); ?>

    <?= $form->field($model, 'closed_at', ['options' => ['class' => '']])->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'data chiusura'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy HH:ii',
                'todayHighlight' => true,
                'autoclose'=>true
            ]
        ])->label('Data chiusura'); ?>
            

    <div class="form-group">
        <?= Html::submitButton('Aggiorna', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>