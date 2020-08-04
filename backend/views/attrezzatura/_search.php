<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzaturaQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-attrezzatura-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idcategoria') ?>

    <?= $form->field($model, 'idtipo') ?>

    <?= $form->field($model, 'classe') ?>

    <?= $form->field($model, 'sottoclasse') ?>

    

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
