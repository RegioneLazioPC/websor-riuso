<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\VolOrganizzazione;
use common\models\LocComune;
use common\models\UtlSpecializzazione;
/* @var $this yii\web\View */
/* @var $model common\models\VolSede */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vol-sede-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= 
    $form->field($model, 'id_organizzazione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( VolOrganizzazione::find()->all(), 'id', 'denominazione'),
        'options' => [
            'placeholder' => 'Organizzazione...',
            'ng-model' => 'ctrl.org',
            'ng-disabled' => 'ctrl.org_',
            'ng-init' => "ctrl.org = '".$model->id_organizzazione."'"
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

    ?>

    <?= $form->field($model, 'indirizzo')->textInput(['maxlength' => true]) ?>

    <?= 
    $form->field($model, 'comune', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( LocComune::find()->all(), 'id', 'comune'),
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

    <?= 
    $form->field($model, 'id_specializzazione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( UtlSpecializzazione::find()->all(), 'id', 'descrizione'),
        'options' => [
            'placeholder' => 'Specializzazione...',
            'ng-model' => 'ctrl.spec',
            'ng-disabled' => 'ctrl.desc',
            'ng-init' => "ctrl.spec = '".$model->id_specializzazione."'"
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
