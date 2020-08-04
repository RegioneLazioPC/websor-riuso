<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VolSedeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vol-sede-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_organizzazione') ?>

    <?= $form->field($model, 'indirizzo') ?>

    <?= $form->field($model, 'comune') ?>

    <?= $form->field($model, 'tipo') ?>


    <div class="form-group">
        <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
