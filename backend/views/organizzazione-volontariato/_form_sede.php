<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\LocComune;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\VolSede */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vol-sede-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'indirizzo')->textInput(['maxlength' => true]) ?>

    <?= 
    $form->field($model, 'comune', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( LocComune::find()->where(
        [
            Yii::$app->params['region_filter_operator'], 
            'id_regione', 
            Yii::$app->params['region_filter_id']
        ])->orderBy(['comune'=>SORT_ASC])->all(), 'id', 'comune'),
        'options' => [
            'placeholder' => 'Comune...',
            'ng-model' => 'ctrl.comune',
            'ng-disabled' => 'ctrl.luogo',
            'ng-init' => "ctrl.comune = '".$model->comune."'"
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

    ?>

    <?= $form->field($model, 'tipo')->dropDownList([ 'Sede Legale' => 'Sede Legale', 'Sede Operativa' => 'Sede Operativa', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email_pec')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cellulare')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'altro_telefono')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sitoweb')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'disponibilita_oraria')->textInput() ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'lon')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
