<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-ingaggio-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idevento') ?>

    <?= $form->field($model, 'idorganizzazione') ?>

    <?= $form->field($model, 'idsede') ?>

    <?= $form->field($model, 'idautomezzo') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
