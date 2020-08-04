<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-automezzo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'targa') ?>

    <?= $form->field($model, 'data_immatricolazione') ?>

    <?= $form->field($model, 'idsquadra') ?>

    <?= $form->field($model, 'classe') ?>

    <?php // echo $form->field($model, 'sottoclasse') ?>

    <?php // echo $form->field($model, 'modello') ?>

    <?php // echo $form->field($model, 'idcategoria') ?>

    <?php // echo $form->field($model, 'idtipo') ?>

    <?php // echo $form->field($model, 'capacita') ?>

    <?php // echo $form->field($model, 'disponibilita') ?>

    <?php // echo $form->field($model, 'idorganizzazione') ?>

    <?php // echo $form->field($model, 'idsede') ?>

    <div class="form-group">
        <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
