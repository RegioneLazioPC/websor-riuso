<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSpecializzazione */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-specializzazione-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descrizione')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Aggiungi', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
