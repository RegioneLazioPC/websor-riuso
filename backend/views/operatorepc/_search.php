<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlOperatorePcSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-operatore-pc-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idsalaoperativa') ?>

    <?= $form->field($model, 'iduser') ?>

    <?= $form->field($model, 'anagrafica.nome') ?>

    <?= $form->field($model, 'anagrafica.cognome') ?>

    <div class="form-group">
        <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
