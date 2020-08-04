<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mass-message-template-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index-template'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'mail_body') ?>

    <?= $form->field($model, 'sms_body') ?>

    <?= $form->field($model, 'push_body') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
