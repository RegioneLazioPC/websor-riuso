<?php

use common\models\LocComune;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSquadraOperativa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-squadra-operativa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome', ['options' => ['class'=>'col-lg-3 no-pl']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'caposquadra', ['options' => ['class'=>'col-lg-3']])->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'idcomune', ['options' => ['class'=>'col-lg-3']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( LocComune::find()->where(['id_regione' => 18])->all(), 'id', 'comune'),
        'options' => [
            'placeholder' => 'Seleziona un comune...'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'numero_membri', ['options' => ['class'=>'col-lg-3']])->textInput() ?>

    <?= $form->field($model, 'tel_caposquadra',['options' => ['class'=>'col-lg-3 no-pl']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cell_caposquadra',['options' => ['class'=>'col-lg-3 ']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frequenza_tras',['options' => ['class'=>'col-lg-3']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frequenza_ric',['options' => ['class'=>'col-lg-3']])->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
