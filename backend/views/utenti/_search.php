<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlUtenteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-utente-search panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><em class="fa fa-search"></em> Ricerca utenti</h3>
    </div>
    <div class="panel-body">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <?= $form->field($model, 'nome', ['options'=>['class' => 'col-lg-2']]) ?>

    <?= $form->field($model, 'cognome', ['options'=>['class' => 'col-lg-2']]) ?>

    <?php echo $form->field($model, 'telefono', ['options'=>['class' => 'col-lg-2']]) ?>

    <?= $form->field($model, 'data_registrazione_dal', ['options' => ['class' => 'col-lg-2']])->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Data dal'],
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
            'todayHighlight' => true,
            'autoclose'=>true
        ]
    ]); ?>

    <?= $form->field($model, 'data_registrazione_al', ['options' => ['class' => 'col-lg-2']])->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Data al'],
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
            'todayHighlight' => true,
            'autoclose'=>true
        ]
    ]); ?>


    <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary m30h']) ?>
    <?= Html::resetButton('Annulla', ['class' => 'btn btn-default m30h']) ?>


    <?php ActiveForm::end(); ?>

    </div>
</div>

