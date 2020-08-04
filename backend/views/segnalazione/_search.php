<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazioneSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-segnalazione-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idutente') ?>

    <?= $form->field($model, 'foto') ?>

    <?= $form->field($model, 'tipologia_evento') ?>

    <?= $form->field($model, 'note') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
