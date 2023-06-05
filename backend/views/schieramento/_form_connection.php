<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

use yii\base\DynamicModel;

$model = new DynamicModel(compact('tipo', 'id', 'date_from', 'date_to'));
?>

<div class="utl-schieramento-connection-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipo')->hiddenInput() ?>
    <?= $form->field($model, 'id')->hiddenInput() ?>

    <?php $form->field($model, 'date_from')->widget(DatePicker::classname(), [
    'options' => ['placeholder' => 'Inserisci la data ...'],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd'
    ]])->label('Valido dal'); ?>
    <?php $form->field($model, 'date_from')->widget(DatePicker::classname(), [
    'options' => ['placeholder' => 'Inserisci la data ...'],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd'
    ]])->label('Valido al'); ?>

    <div class="form-group">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Modifica', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>